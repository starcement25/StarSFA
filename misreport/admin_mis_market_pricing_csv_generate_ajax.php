<?php
/* ==========================================================================
   MARKET FEEDBACK - PRICING : server side CSV generator (worker)
   --------------------------------------------------------------------------
   Used by admin_mis_market_pricing_csv_generate.php.

   A long range (Apr'26 - Sep'26) is too big to render in the browser or to
   stream in one HTTP request, so the report is built on the server one
   calendar month per request and the finished CSV is kept in
   generated_reports/market_pricing/ for download later.

   action = start     create a job, write the CSV header, split range by month
            run       process the next pending month (one per request)
            status    job progress
            list      all generated / in-progress reports
            download  send a finished CSV
            delete    remove a report

   Columns are the same as admin_mis_market_pricing_data_customize_modified.php
   ========================================================================== */

ob_start();
session_start();

require("adminUtils.php");
require("include/config.php");
require("include/config-setup.php");
require("include/dbcon.php");

if(!isset($_SESSION['admin_login']) || $_SESSION['admin_login']==""){
    json_out(array('ok' => false, 'msg' => 'Session expired. Please login again.'));
}

$admin_user = $_SESSION['admin_login'];

/* release the session lock, otherwise the status polls wait behind a long
   running 'run' request of the same user */
session_write_close();

define('REPORT_DIR', __DIR__ . '/generated_reports/market_pricing');

if(!is_dir(REPORT_DIR)){
    @mkdir(REPORT_DIR, 0775, true);
}

if(!is_dir(REPORT_DIR) || !is_writable(REPORT_DIR)){
    json_out(array('ok' => false, 'msg' => 'Report folder is not writable : misreport/generated_reports/market_pricing'));
}

/* deny direct web access to the stored files - downloads go through this script */
if(!file_exists(REPORT_DIR . '/.htaccess')){
    @file_put_contents(REPORT_DIR . '/.htaccess',
        "<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n"
      . "<IfModule !mod_authz_core.c>\n    Order allow,deny\n    Deny from all\n</IfModule>\n");
}

if(!file_exists(REPORT_DIR . '/index.html')){
    @file_put_contents(REPORT_DIR . '/index.html', '');
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

switch($action){

    case 'start':    action_start($link, $admin_user); break;
    case 'run':      action_run($link);                break;
    case 'status':   action_status();                  break;
    case 'list':     action_list();                    break;
    case 'download': action_download();                break;
    case 'delete':   action_delete();                  break;

    default:
        json_out(array('ok' => false, 'msg' => 'Invalid action'));
}


/* ================= START ================= */

function action_start($link, $admin_user)
{
    $start_date = isset($_POST['start_date']) ? trim($_POST['start_date']) : '';
    $end_date   = isset($_POST['end_date'])   ? trim($_POST['end_date'])   : '';
    $branch     = isset($_POST['branch'])     ? trim($_POST['branch'])     : '';
    $employee   = isset($_POST['employee'])   ? trim($_POST['employee'])   : '';
    $zone       = isset($_POST['zone'])       ? trim($_POST['zone'])       : '';
    $state      = isset($_POST['state'])      ? trim($_POST['state'])      : '';
    $filter_txt = isset($_POST['filter_text']) ? trim($_POST['filter_text']) : '';

    $start_key = normalize_date_key($start_date);
    $end_key   = normalize_date_key($end_date);

    if($start_key === false || $end_key === false){
        json_out(array('ok' => false, 'msg' => 'Invalid start date / end date.'));
    }

    if($start_key > $end_key){
        json_out(array('ok' => false, 'msg' => 'Start date cannot be greater than end date.'));
    }

    /* UI sends the list as 'E0001','E0002' (or with # for comma and ^ for quote) */
    $employee_arg = str_replace("#", ",", $employee);
    $employee_arg = str_replace("^", "'", $employee_arg);

    $employee_list = array();

    foreach(explode(",", $employee_arg) as $emp){

        $emp = trim(trim($emp), "'");

        if($emp === '') continue;

        $employee_list[] = "'" . mysqli_real_escape_string($link, $emp) . "'";
    }

    $employee_list = array_values(array_unique($employee_list));

    if(empty($employee_list)){
        json_out(array('ok' => false, 'msg' => 'No employee selected.'));
    }

    /* ---------- competitor list (same as the on-screen report) ---------- */

    $branchcondition = ($branch == "all" || $branch == "")
        ? ''
        : " AND branch_code='" . mysqli_real_escape_string($link, $branch) . "'";

    $sql_competitor_name = "SELECT DISTINCT competitor_name, acedns
                            FROM competitor_group_master
                            WHERE acedns='yes'
                            $branchcondition
                            AND branch_code IS NOT NULL
                            AND branch_code!=''
                            ORDER BY FIELD(competitor_name, 'STAR PSC','STAR PPC','STAR') DESC,
                                     competitor_name ASC";

    $res_competitor_name = mysqli_query($link, $sql_competitor_name);

    $competitor_name_array = array();

    while($row_competitor_name = mysqli_fetch_assoc($res_competitor_name)){
        $competitor_name_array[] = $row_competitor_name['competitor_name'];
    }

    mysqli_free_result($res_competitor_name);

    if(empty($competitor_name_array)){
        json_out(array('ok' => false, 'msg' => 'No competitor configured for this branch.'));
    }

    /* ---------- split the range by calendar month, latest month first,
                  so the file keeps the report's date DESC order ---------- */

    $chunks = array();

    $cursor = DateTime::createFromFormat('Ymd', $end_key);
    $first  = DateTime::createFromFormat('Ymd', $start_key);

    while($cursor->format('Ymd') >= $first->format('Ymd')){

        $chunk_to   = $cursor->format('Ymd');
        $month_from = $cursor->format('Ym') . '01';
        $chunk_from = ($month_from < $start_key) ? $start_key : $month_from;

        $chunks[] = array(
            'from'  => $chunk_from,
            'to'    => $chunk_to,
            'label' => $cursor->format("M'y")
        );

        /* last day of the previous month */
        $cursor = DateTime::createFromFormat('Ymd', $month_from);
        $cursor->modify('-1 day');
    }

    /* ---------- job ---------- */

    $job_id   = date('Ymd_His') . '_' . substr(md5(uniqid('', true)), 0, 6);
    $filename = "Market_Feedback_Pricing_" . $start_key . "_to_" . $end_key . "_" . date('Ymd_His') . ".csv";

    $job = array(
        'id'            => $job_id,
        'filename'      => $filename,
        'created_by'    => $admin_user,
        'created_at'    => date('d-m-Y H:i:s'),
        'finished_at'   => '',
        'start_key'     => $start_key,
        'end_key'       => $end_key,
        'zone'          => $zone,
        'state'         => $state,
        'branch'        => $branch,
        'filter_text'   => $filter_txt,
        'employee_in'   => implode(",", $employee_list),
        'emp_count'     => count($employee_list),
        'competitors'   => $competitor_name_array,
        'chunks'        => $chunks,
        'next_chunk'    => 0,
        'rows'          => 0,
        'chunk_rows'    => 0,
        'status'        => 'pending',
        'error'         => ''
    );

    /* ---------- header rows ---------- */

    $fixed_headers = fixed_headers();

    $head1 = array_fill(0, count($fixed_headers), "");
    $head2 = $fixed_headers;

    foreach($competitor_name_array as $competitor_name){

        /* name sits over the first of its five columns */
        array_push($head1, $competitor_name, "", "", "", "");
        array_push($head2, "Billing Ex", "Billing For", "WSP Ex", "WSP For", "RSP");
    }

    $fp = fopen(job_path($job_id, 'part'), 'w');

    if(!$fp){
        json_out(array('ok' => false, 'msg' => 'Could not create the report file on server.'));
    }

    /* BOM so Excel reads UTF-8 correctly */
    fwrite($fp, "\xEF\xBB\xBF");
    fputcsv($fp, $head1);
    fputcsv($fp, $head2);
    fclose($fp);

    save_job($job);

    json_out(array('ok' => true, 'job' => public_job($job)));
}


/* ================= RUN : one month per request ================= */

function action_run($link)
{
    $job_id = request_job_id();
    $job    = load_job($job_id);

    if($job['status'] == 'done'){
        json_out(array('ok' => true, 'job' => public_job($job)));
    }

    /* only one worker per job */
    $lock = fopen(job_path($job_id, 'lock'), 'c');

    if(!$lock || !flock($lock, LOCK_EX | LOCK_NB)){
        json_out(array('ok' => true, 'busy' => true, 'job' => public_job($job, true)));
    }

    if(function_exists('set_time_limit')) @set_time_limit(0);
    ignore_user_abort(true);
    @ini_set('memory_limit', '1024M');

    /* PHP 8.1+ throws on a failed query - turn that off so the error is
       read with mysqli_error() and saved on the job instead of a blank 500 */
    mysqli_report(MYSQLI_REPORT_OFF);

    /* any fatal error (memory, time limit ...) is recorded on the job */
    register_shutdown_function('run_shutdown', $job_id);

    /* re-read under the lock */
    $job = load_job($job_id);

    $index = (int)$job['next_chunk'];

    if($index >= count($job['chunks'])){
        finish_job($job);
        flock($lock, LOCK_UN);
        fclose($lock);
        json_out(array('ok' => true, 'job' => public_job($job)));
    }

    $chunk = $job['chunks'][$index];

    $job['status']     = 'running';
    $job['chunk_rows'] = 0;
    $job['error']      = '';
    save_job($job);

    $competitor_string = '';

    foreach($job['competitors'] as $competitor_name){
        $competitor_string .= "'" . mysqli_real_escape_string($link, $competitor_name) . "',";
    }

    $competitor_string = rtrim($competitor_string, ",");

    $employee_in = $job['employee_in'];
    $from_key    = $chunk['from'];
    $to_key      = $chunk['to'];

    /* Ordered so every row of one (date, employee, customer) arrives
       together - lets the loop write a line as soon as the group changes. */
    $sql_competitor_stock = "
        SELECT
            CM.customer_name,
            CM.phone_no,
            CM.cust_type,
            CM.dns_customer_code,
            RM.route_name,
            EM.emp_name,
            EM.dns_emp_code,
            SUM(MF.PTD) AS PTD,
            SUM(MF.PTR) AS PTR,
            SUM(MF.PTC) AS PTC,
            SUM(MF.PV) AS PV,
            SUM(MF.billing_ex_for) AS billing_ex_for,
            SUM(MF.wsp_ex_for) AS wsp_ex_for,
            DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y') AS visit_date,
            SUBSTRING(MF.market_feedback_id, -14, 8) AS visit_date_sort,
            BM.branch_name,
            MF.competitor_name,
            SUBSTRING(MF.market_feedback_id, 3, 5) AS emp_code,
            MF.customer_code
        FROM
            market_feedback MF,
            customer_master CM,
            employee_master EM,
            route_master RM,
            branch_master BM
        WHERE
            MF.customer_code = CM.customer_code
            AND CM.route_code = RM.route_code
            AND SUBSTRING(MF.market_feedback_id, 3, 5) = EM.emp_code
            AND SUBSTRING(MF.market_feedback_id, 3, 5) IN ($employee_in)
            AND SUBSTRING(MF.market_feedback_id, -14, 8) BETWEEN '$from_key' AND '$to_key'
            AND MF.competitor_name IN ($competitor_string)
            AND CM.branch_code = BM.branch_code
        GROUP BY
            CM.customer_name,
            CM.phone_no,
            CM.cust_type,
            CM.dns_customer_code,
            RM.route_name,
            EM.emp_name,
            EM.dns_emp_code,
            visit_date_sort,
            BM.branch_name,
            MF.competitor_name,
            emp_code,
            MF.customer_code,
            DATE_FORMAT(SUBSTRING(MF.market_feedback_id, -14, 8), '%d-%m-%Y')
        ORDER BY
            visit_date_sort DESC,
            EM.dns_emp_code ASC,
            CM.dns_customer_code ASC,
            FIELD(MF.competitor_name, 'STAR PSC', 'STAR PPC', 'STAR') DESC,
            MF.competitor_name ASC
    ";

    /* the month is written to its own temp file first and appended to the
       report only when complete - a killed request can simply be re-run */
    $tmp_path = job_path($job_id, 'tmp');
    $out      = fopen($tmp_path, 'w');

    if(!mysqli_real_query($link, $sql_competitor_stock)){

        fclose($out);
        @unlink($tmp_path);

        $job['status'] = 'error';
        $job['error']  = $chunk['label'] . ' : ' . mysqli_error($link);
        save_job($job);

        flock($lock, LOCK_UN);
        fclose($lock);
        json_out(array('ok' => false, 'msg' => $job['error'], 'job' => public_job($job)));
    }

    /* unbuffered - rows are pulled one at a time */
    $res_competitor_stock = mysqli_use_result($link);

    $count       = (int)$job['rows'] + 1;
    $written     = 0;
    $current_key = null;
    $current     = null;
    $competitors = array();

    while($row = mysqli_fetch_assoc($res_competitor_stock)){

        $key = $row['dns_emp_code'] . '#' . $row['dns_customer_code'] . '#' . $row['visit_date'];

        if($key !== $current_key){

            /* previous group is complete - write it out */
            if($current_key !== null){

                writeRow($out, $count, $current, $competitors, $job['competitors']);
                $count++;
                $written++;

                if($written % 2000 == 0){
                    $job['chunk_rows'] = $written;
                    save_job($job);
                }
            }

            $current_key = $key;
            $competitors = array();

            $current = array(
                'visit_date'        => $row['visit_date'],
                'dns_emp_code'      => $row['dns_emp_code'],
                'emp_name'          => $row['emp_name'],
                'branch_name'       => $row['branch_name'],
                'cust_type'         => $row['cust_type'],
                'dns_customer_code' => $row['dns_customer_code'],
                'customer_name'     => $row['customer_name'],
                'route_name'        => $row['route_name'],
                'phone_no'          => $row['phone_no']
            );
        }

        $competitors[$row['competitor_name']] = array(
            'PTD'            => $row['PTD'],
            'PTR'            => $row['PTR'],
            'PTC'            => $row['PTC'],
            'billing_ex_for' => $row['billing_ex_for'],
            'wsp_ex_for'     => $row['wsp_ex_for']
        );
    }

    /* last group */
    if($current_key !== null){
        writeRow($out, $count, $current, $competitors, $job['competitors']);
        $written++;
    }

    mysqli_free_result($res_competitor_stock);
    fclose($out);

    /* ---------- append the month to the report ---------- */

    $src = fopen($tmp_path, 'r');
    $dst = fopen(job_path($job_id, 'part'), 'a');

    stream_copy_to_stream($src, $dst);

    fclose($src);
    fclose($dst);
    @unlink($tmp_path);

    $job['rows']       = (int)$job['rows'] + $written;
    $job['chunk_rows'] = 0;
    $job['next_chunk'] = $index + 1;
    $job['status']     = 'pending';

    if($job['next_chunk'] >= count($job['chunks'])){
        finish_job($job);
    }
    else{
        save_job($job);
    }

    flock($lock, LOCK_UN);
    fclose($lock);

    json_out(array('ok' => true, 'job' => public_job($job)));
}


/* ================= STATUS ================= */

function action_status()
{
    $job = load_job(request_job_id());

    json_out(array('ok' => true, 'job' => public_job($job, is_job_running($job['id']))));
}


/* ================= LIST ================= */

function action_list()
{
    $jobs = array();

    foreach((array)glob(REPORT_DIR . '/*.json') as $file){

        $job = json_decode(file_get_contents($file), true);

        if(!is_array($job) || !isset($job['id'])) continue;

        $jobs[] = public_job($job, is_job_running($job['id']));
    }

    /* newest first - job id starts with Ymd_His */
    usort($jobs, function($a, $b){ return strcmp($b['id'], $a['id']); });

    json_out(array('ok' => true, 'jobs' => $jobs));
}


/* ================= DOWNLOAD ================= */

function action_download()
{
    $job  = load_job(request_job_id());
    $path = job_path($job['id'], 'csv');

    if($job['status'] != 'done' || !file_exists($path)){
        while(ob_get_level()) ob_end_clean();
        echo "Report is not ready yet.";
        exit;
    }

    while(ob_get_level()) ob_end_clean();

    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"" . $job['filename'] . "\"");
    header("Content-Length: " . filesize($path));
    header("Pragma: no-cache");
    header("Expires: 0");

    readfile($path);
    exit;
}


/* ================= DELETE ================= */

function action_delete()
{
    $job_id = request_job_id();

    if(is_job_running($job_id)){
        json_out(array('ok' => false, 'msg' => 'Report is being generated right now. Try again after it finishes.'));
    }

    foreach(array('json', 'csv', 'part', 'tmp', 'lock') as $type){
        @unlink(job_path($job_id, $type));
    }

    json_out(array('ok' => true));
}


/* ================= HELPERS ================= */

function fixed_headers()
{
    return array(
        "SI. No",
        "Date of Visit",
        "Emp Code",
        "Emp Name",
        "Branch",
        "Cust Category",
        "Cust Code",
        "Cust Name",
        "Route Name",
        "Contact No"
    );
}

function job_path($job_id, $type)
{
    $ext = array(
        'json' => '.json',
        'csv'  => '.csv',
        'part' => '.part.csv',
        'tmp'  => '.chunk.tmp',
        'lock' => '.lock'
    );

    return REPORT_DIR . '/' . $job_id . $ext[$type];
}

function request_job_id()
{
    $job_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';

    /* id is used in file paths - allow only the format we generate */
    if(!preg_match('/^\d{8}_\d{6}_[a-f0-9]{6}$/', $job_id)){
        json_out(array('ok' => false, 'msg' => 'Invalid report id'));
    }

    return $job_id;
}

function load_job($job_id)
{
    $path = job_path($job_id, 'json');

    if(!file_exists($path)){
        json_out(array('ok' => false, 'msg' => 'Report not found'));
    }

    $job = json_decode(file_get_contents($path), true);

    if(!is_array($job)){
        json_out(array('ok' => false, 'msg' => 'Report details are unreadable'));
    }

    return $job;
}

function save_job($job)
{
    file_put_contents(job_path($job['id'], 'json'), json_encode($job), LOCK_EX);
}

function finish_job(&$job)
{
    $part = job_path($job['id'], 'part');

    if((int)$job['rows'] == 0){
        $fp = fopen($part, 'a');
        fputcsv($fp, array("No Records found"));
        fclose($fp);
    }

    rename($part, job_path($job['id'], 'csv'));

    $job['status']      = 'done';
    $job['chunk_rows']  = 0;
    $job['finished_at'] = date('d-m-Y H:i:s');

    save_job($job);
}

function run_shutdown($job_id)
{
    $err = error_get_last();

    if(!$err || !in_array($err['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR))) return;

    $path = job_path($job_id, 'json');
    $job  = json_decode(@file_get_contents($path), true);

    if(!is_array($job) || $job['status'] == 'done') return;

    $index = (int)$job['next_chunk'];
    $label = isset($job['chunks'][$index]) ? $job['chunks'][$index]['label'] . ' : ' : '';

    $job['status']     = 'error';
    $job['chunk_rows'] = 0;
    $job['error']      = $label . $err['message'] . ' (line ' . $err['line'] . ')';

    @file_put_contents($path, json_encode($job), LOCK_EX);
    @unlink(job_path($job_id, 'tmp'));
}

function is_job_running($job_id)
{
    $path = job_path($job_id, 'lock');

    if(!file_exists($path)) return false;

    $fp = fopen($path, 'c');

    if(!$fp) return false;

    if(flock($fp, LOCK_EX | LOCK_NB)){
        flock($fp, LOCK_UN);
        fclose($fp);
        return false;
    }

    fclose($fp);
    return true;
}

/* what the browser needs - leaves out the long employee list */
function public_job($job, $running = false)
{
    $total = count($job['chunks']);
    $index = (int)$job['next_chunk'];

    $current_label = ($index < $total) ? $job['chunks'][$index]['label'] : '';

    $size = 0;

    if($job['status'] == 'done' && file_exists(job_path($job['id'], 'csv'))){
        $size = filesize(job_path($job['id'], 'csv'));
    }

    return array(
        'id'            => $job['id'],
        'filename'      => $job['filename'],
        'created_by'    => $job['created_by'],
        'created_at'    => $job['created_at'],
        'finished_at'   => $job['finished_at'],
        'from'          => date_label($job['start_key']),
        'to'            => date_label($job['end_key']),
        'branch'        => $job['branch'],
        'filter_text'   => $job['filter_text'],
        'emp_count'     => $job['emp_count'],
        'status'        => $running ? 'running' : ($job['status'] == 'running' ? 'stopped' : $job['status']),
        'error'         => $job['error'],
        'rows'          => (int)$job['rows'] + (int)$job['chunk_rows'],
        'months_done'   => $index,
        'months_total'  => $total,
        'current_month' => $current_label,
        'size'          => $size
    );
}

function date_label($key)
{
    return substr($key, 6, 2) . '-' . substr($key, 4, 2) . '-' . substr($key, 0, 4);
}

function json_out($data)
{
    while(ob_get_level()) ob_end_clean();

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data);
    exit;
}

/* Returns the date as YYYYMMDD, or false when it cannot be read.
   Handles YYYY-MM-DD and DD-MM-YYYY, separated by - or / */
function normalize_date_key($date)
{
    $date = str_replace('/', '-', trim($date));

    if($date === '') return false;

    /* YYYY-MM-DD */
    if(preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $date, $m)){
        $year  = $m[1];
        $month = $m[2];
        $day   = $m[3];
    }
    /* DD-MM-YYYY */
    else if(preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $date, $m)){
        $day   = $m[1];
        $month = $m[2];
        $year  = $m[3];
    }
    else{
        return false;
    }

    if(!checkdate((int)$month, (int)$day, (int)$year)) return false;

    return $year . str_pad($month, 2, '0', STR_PAD_LEFT) . str_pad($day, 2, '0', STR_PAD_LEFT);
}

function writeRow($out, $count, $current, $competitors, $competitor_name_array)
{
    $line = array(
        $count,
        $current['visit_date'],
        $current['dns_emp_code'],
        $current['emp_name'],
        $current['branch_name'],
        $current['cust_type'],
        $current['dns_customer_code'],
        $current['customer_name'],
        $current['route_name'],
        $current['phone_no']
    );

    foreach($competitor_name_array as $competitorval){

        if(isset($competitors[$competitorval])){

            $c = $competitors[$competitorval];

            $line[] = ($c['PTD'] == 0)            ? '-' : $c['PTD'];
            $line[] = ($c['billing_ex_for'] == 0) ? '-' : $c['billing_ex_for'];
            $line[] = ($c['PTR'] == 0)            ? '-' : $c['PTR'];
            $line[] = ($c['wsp_ex_for'] == 0)     ? '-' : $c['wsp_ex_for'];
            $line[] = ($c['PTC'] == 0)            ? '-' : $c['PTC'];
        }
        else{
            array_push($line, '-', '-', '-', '-', '-');
        }
    }

    fputcsv($out, $line);
}
