<?php
/* ==========================================================================
   MARKET FEEDBACK - PRICING : generate CSV on server
   --------------------------------------------------------------------------
   For long date ranges (e.g. Apr'26 - Sep'26) that are too big to show in
   the browser. The report is built on the server month by month and saved
   in misreport/generated_reports/market_pricing/, from where it can be
   downloaded any time. Work is done by
   admin_mis_market_pricing_csv_generate_ajax.php
   ========================================================================== */

ob_start();
session_start();
require("adminUtils.php");

require("include/config.php");
require("include/config-setup.php");

require ("attribute_selection.php");
if($_SESSION['admin_login']=="") { header("location:index.php"); exit; }

disphtml("main();");

function main(){
    require("include/dbcon.php");

    /* default range : start of the financial year (1st April) to today */
    $fy_year     = (date('n') >= 4) ? date('Y') : date('Y') - 1;
    $default_from = $fy_year . '-04-01';
    $default_to   = date('Y-m-d');
	?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript" src="ajax1.js"></script>
    <style>
        .gen_box      { width:95%; margin:0 auto; text-align:left; font-size:12px; }
        .gen_note     { background:#fff8e1; border:1px solid #e0c060; padding:8px 10px; margin-bottom:10px; }
        .gen_progress { border:1px solid #999; background:#eee; height:18px; width:100%; margin:6px 0; }
        .gen_bar      { background:#a52a5a; height:18px; width:0%; color:#fff; text-align:center; line-height:18px; font-size:11px; }
        .gen_list td  { padding:4px 6px; font-size:12px; }
        .gen_list .TDHEAD_SUB td { font-weight:bold; }
        .st_done      { color:green; font-weight:bold; }
        .st_running   { color:#0055cc; font-weight:bold; }
        .st_error     { color:red; font-weight:bold; }
        .st_stopped, .st_pending { color:#b36b00; font-weight:bold; }
        .act_btn      { display:inline-block; padding:4px 10px; margin:2px; border-radius:3px; color:#fff !important; font-size:12px; font-weight:bold; text-decoration:none; cursor:pointer; white-space:nowrap; }
        .act_btn:hover { opacity:0.85; }
        .act_download { background:#2e7d32; }
        .act_resume   { background:#0055cc; }
        .act_delete   { background:#c62828; }
    </style>
    <?php
	$hidden = "";
	echo "<center>";
	echo "<span style=\"font-weight:bold; font-size:14px;\">MARKET FEEDBACK - PRICING : GENERATE CSV ON SERVER</span><br><br>";
	echo "<table width='100%'><tr><td align='left' valign='top' style='padding-left:10px;'><a href='admin_mis_market_pricing_customize.php' style='color:blue; font-weight:bold;'><< Back</a></td><td width='90%' align='center'>";
	attribute_selection($hidden,"");
	echo "</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr></table>";
	echo "<br>";
	?>
    <div class="gen_box">
        <div class="gen_note">
            Select the criteria and date range above (e.g. 01-04-2026 to 30-09-2026) and press <b>Generate CSV</b>.
            The report is <b>not shown on screen</b>. It is built on the server one month at a time and saved there,
            and you can download it from the list below at any time. Keep this page open until it finishes.
            If you close the page, press <b>Resume</b> on the report later.
        </div>

        <input type="button" id="btnGenerate" value="Generate CSV" onclick="generate_csv();" style="font-weight:bold; padding:3px 12px;">

        <div id="gen_status_box" style="display:none; margin-top:10px;">
            <div id="gen_status_text"></div>
            <div class="gen_progress"><div class="gen_bar" id="gen_bar">0%</div></div>
        </div>

        <br><br>
        <b>Generated Reports</b> &nbsp; <a href="javascript:void(0);" onclick="load_list();" style="color:blue;">Refresh</a>
        <div id="gen_list" style="margin-top:6px;">Loading...</div>
    </div>
    <?php
	echo "</center>";
	?>
    <script>
	var GEN_URL    = 'admin_mis_market_pricing_csv_generate_ajax.php';
	var active_job = null;
	var fail_count = 0;
	var last_done  = -1;	/* months_done when the last month was started */
	var retry_count = 0;

	$(function(){
		/* default range : financial year start to today */
		if(document.getElementById("start_date")) document.getElementById("start_date").value = '<?php echo $default_from; ?>';
		if(document.getElementById("end_date"))   document.getElementById("end_date").value   = '<?php echo $default_to; ?>';
		load_list();
	});

	/* Submit button of the criteria box */
	function display_result(){
		generate_csv();
	}

	function selected_text(id){
		var el = document.getElementById(id);
		if(!el || el.selectedIndex < 0) return '';
		return $.trim(el.options[el.selectedIndex].text);
	}

	function generate_csv(){
		if(active_job){
			alert('A report is already being generated. Please wait for it to finish.');
			return false;
		}
		if(document.getElementById("zone").value.search(/\S/) == -1){
			alert('Please Select Zone');
			return false;
		}
		if(document.getElementById("state").value.search(/\S/) == -1){
			alert('Please Select State');
			return false;
		}
		if(document.getElementById("branch").value.search(/\S/) == -1){
			alert('Please Select Branch');
			return false;
		}
		if(document.getElementById("sale_access"))
		{
			if(document.getElementById("sale_access").value.search(/\S/) == -1){
				alert('Please Select Department');
				return false;
			}
		}
		if(document.getElementById("employee").value.search(/\S/) == -1){
			alert('Please Select Employee');
			return false;
		}

		var start_date = document.getElementById("start_date").value;
		var end_date   = document.getElementById("end_date").value;

		if(start_date.search(/\S/) == -1 || end_date.search(/\S/) == -1){
			alert("Please provide start date/end date");
			return false;
		}
		if(start_date > end_date){
			alert("Start date cannot be greater than end date");
			return false;
		}

		var filter_text = 'Zone: ' + selected_text('zone')
		                + ', State: ' + selected_text('state')
		                + ', Branch: ' + selected_text('branch')
		                + (document.getElementById("sale_access") ? ', Dept: ' + selected_text('sale_access') : '')
		                + ', Employee: ' + selected_text('employee');

		$('#btnGenerate').prop('disabled', true);
		show_status('Starting...', 0);

		/* POST - the employee list is too long for a URL */
		$.ajax({
			url: GEN_URL,
			type: 'POST',
			dataType: 'json',
			data: {
				action:      'start',
				employee:    document.getElementById("employee").value,
				branch:      document.getElementById("branch").value,
				zone:        document.getElementById("zone").value,
				state:       document.getElementById("state").value,
				start_date:  start_date,
				end_date:    end_date,
				filter_text: filter_text
			},
			success: function(res){
				if(!res.ok){
					alert(res.msg);
					reset_generate();
					return;
				}
				load_list();
				run_job(res.job.id);
			},
			error: function(xhr){
				alert('Could not start the report. ' + xhr.status + ' ' + xhr.statusText);
				reset_generate();
			}
		});
	}

	/* Each 'run' call builds one month. A call can outlive the web server's
	   timeout, so whatever happens to it we poll 'status' and only fire the
	   next month once the server says the job is idle. */
	function run_job(id){
		active_job  = id;
		fail_count  = 0;
		last_done   = -1;
		retry_count = 0;
		$('#btnGenerate').prop('disabled', true);
		next_step();
	}

	function next_step(){
		$.ajax({
			url: GEN_URL,
			type: 'POST',
			dataType: 'json',
			data: { action: 'run', id: active_job },
			timeout: 0,
			success: function(res){ after_run(res); },
			error:   function(){ poll_status(); }
		});
		/* show progress while the month is being built */
		setTimeout(poll_progress, 4000);
	}

	function after_run(res){
		if(!res.ok && !res.job){
			job_failed(res.msg);
			return;
		}
		handle_job(res.job, !!res.busy);
	}

	function poll_status(){
		$.ajax({
			url: GEN_URL,
			type: 'POST',
			dataType: 'json',
			data: { action: 'status', id: active_job },
			success: function(res){
				if(!res.ok){ job_failed(res.msg); return; }
				handle_job(res.job, false);
			},
			error: function(){
				fail_count++;
				if(fail_count > 5){ job_failed('Server is not responding.'); return; }
				setTimeout(poll_status, 5000);
			}
		});
	}

	/* progress only - does not drive the job */
	function poll_progress(){
		if(!active_job) return;
		$.ajax({
			url: GEN_URL,
			type: 'POST',
			dataType: 'json',
			data: { action: 'status', id: active_job },
			success: function(res){
				if(res.ok && active_job == res.job.id && res.job.status == 'running'){
					show_job_progress(res.job);
					setTimeout(poll_progress, 4000);
				}
			}
		});
	}

	function handle_job(job, busy){
		show_job_progress(job);

		if(job.status == 'done'){
			active_job = null;
			reset_generate();
			show_status('Completed : ' + job.rows + ' rows. <a href="' + GEN_URL + '?action=download&id=' + job.id + '" class="act_btn act_download">&#8681; Download CSV</a>', 100);
			load_list();
			return;
		}

		if(job.status == 'error'){
			job_failed(job.error);
			return;
		}

		if(job.status == 'running' || busy){
			/* a month is still being built by an earlier request */
			setTimeout(poll_status, 5000);
			return;
		}

		/* pending / stopped : build the next month. If the same month keeps
		   coming back unfinished, stop instead of hammering the server. */
		if(job.months_done == last_done){
			retry_count++;
			if(retry_count >= 2){
				job_failed('Month ' + job.current_month + ' could not be generated after 3 attempts. Please check the server error log.');
				return;
			}
		} else {
			last_done   = job.months_done;
			retry_count = 0;
		}
		fail_count = 0;
		load_list();
		setTimeout(next_step, retry_count ? 5000 : 500);
	}

	function show_job_progress(job){
		var pct  = job.months_total ? Math.floor(job.months_done * 100 / job.months_total) : 0;
		var text = 'Generating ' + job.from + ' to ' + job.to + ' : ';
		if(job.status == 'done'){
			text += 'completed';
		} else {
			text += 'month ' + (job.months_done + 1) + ' of ' + job.months_total
			      + (job.current_month ? ' (' + job.current_month + ')' : '');
		}
		text += ' &nbsp; | &nbsp; Rows written : ' + job.rows;
		show_status(text, pct);
	}

	function job_failed(msg){
		active_job = null;
		reset_generate();
		show_status('<span class="st_error">Stopped : ' + msg + '</span> &nbsp; Use <b>Resume</b> in the list below to continue.', null);
		load_list();
	}

	function show_status(html, pct){
		$('#gen_status_box').show();
		$('#gen_status_text').html(html);
		if(pct !== null){
			$('#gen_bar').css('width', pct + '%').text(pct + '%');
		}
	}

	function reset_generate(){
		$('#btnGenerate').prop('disabled', false);
	}

	function resume_job(id){
		if(active_job){
			alert('A report is already being generated. Please wait for it to finish.');
			return;
		}
		show_status('Resuming...', null);
		run_job(id);
	}

	function delete_job(id){
		if(!confirm('Delete this report from the server?')) return;
		$.post(GEN_URL, { action: 'delete', id: id }, function(res){
			if(!res.ok) alert(res.msg);
			load_list();
		}, 'json');
	}

	function format_size(bytes){
		if(bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
		if(bytes >= 1024)    return (bytes / 1024).toFixed(0) + ' KB';
		return bytes + ' B';
	}

	function esc(s){
		return $('<div>').text(s == null ? '' : String(s)).html();
	}

	function load_list(){
		$.ajax({
			url: GEN_URL,
			type: 'POST',
			dataType: 'json',
			data: { action: 'list' },
			success: function(res){
				if(!res.ok){ $('#gen_list').html(esc(res.msg)); return; }
				if(!res.jobs.length){ $('#gen_list').html('No report generated yet.'); return; }

				var h = '<table border="1" style="border-collapse:collapse;" class="border gen_list" width="100%">'
				      + '<tr class="TDHEAD_SUB"><td>SI. No</td><td>From</td><td>To</td><td>Criteria</td><td>Created By</td>'
				      + '<td>Created On</td><td>Status</td><td>Rows</td><td>Size</td><td>Action</td></tr>';

				$.each(res.jobs, function(i, j){
					var status = j.status;
					var action = '';

					if(j.status == 'done'){
						status = 'Completed<br><small>' + esc(j.finished_at) + '</small>';
						action = '<a href="' + GEN_URL + '?action=download&id=' + j.id + '" class="act_btn act_download">&#8681; Download</a>';
					} else if(j.status == 'running'){
						status = 'Running (' + j.months_done + '/' + j.months_total + ' months)';
					} else {
						status = (j.status == 'error' ? 'Error' : 'Incomplete') + ' (' + j.months_done + '/' + j.months_total + ' months)'
						       + (j.error ? '<br><small>' + esc(j.error) + '</small>' : '');
						action = '<a href="javascript:void(0);" onclick="resume_job(\'' + j.id + '\');" class="act_btn act_resume">&#8635; Resume</a>';
					}

					if(j.status != 'running'){
						action += '<a href="javascript:void(0);" onclick="delete_job(\'' + j.id + '\');" class="act_btn act_delete">&#10006; Delete</a>';
					}

					h += '<tr>'
					   + '<td>' + (i + 1) + '</td>'
					   + '<td>' + esc(j.from) + '</td>'
					   + '<td>' + esc(j.to) + '</td>'
					   + '<td>' + esc(j.filter_text) + '<br><small>' + esc(j.emp_count) + ' employee(s)</small></td>'
					   + '<td>' + esc(j.created_by) + '</td>'
					   + '<td>' + esc(j.created_at) + '</td>'
					   + '<td class="st_' + esc(j.status) + '">' + status + '</td>'
					   + '<td align="right">' + esc(j.rows) + '</td>'
					   + '<td align="right">' + (j.size ? format_size(j.size) : '-') + '</td>'
					   + '<td>' + action + '</td>'
					   + '</tr>';
				});

				h += '</table>';
				$('#gen_list').html(h);
			},
			error: function(xhr){
				$('#gen_list').html('Could not load the list. ' + xhr.status + ' ' + xhr.statusText);
			}
		});
	}
	</script>
    <?php
}
?>
