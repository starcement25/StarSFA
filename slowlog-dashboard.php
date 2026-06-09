<?php
$file = "/var/log/mysql/mysql-slow.log";

if (!file_exists($file)) {
    die("Log file not found");
}

$queries = [];
$current = [];

foreach (file($file) as $line) {

    if (strpos($line, '# Query_time:') !== false) {
        preg_match('/Query_time: ([0-9\.]+)/', $line, $matches);
        $current = [
            'time' => floatval($matches[1]),
            'query' => ''
        ];
    }

    elseif (trim($line) && substr($line, 0, 1) != '#') {
        $current['query'] .= " " . trim($line);
    }

    elseif (!empty($current['query'])) {

        // Normalize query (remove values → group similar queries)
        $normalized = preg_replace('/\d+/', '?', $current['query']);
        $normalized = preg_replace('/\'.*?\'/', '?', $normalized);

        $key = md5($normalized);

        if (!isset($queries[$key])) {
            $queries[$key] = [
                'query' => $normalized,
                'count' => 0,
                'total_time' => 0,
                'max_time' => 0
            ];
        }

        $queries[$key]['count']++;
        $queries[$key]['total_time'] += $current['time'];
        $queries[$key]['max_time'] = max($queries[$key]['max_time'], $current['time']);

        $current = [];
    }
}

// Sort by total_time DESC
usort($queries, fn($a,$b) => $b['total_time'] <=> $a['total_time']);

?>

<!DOCTYPE html>
<html>
<head>
    <title>MySQL Slow Query Dashboard</title>
    <meta http-equiv="refresh" content="15">
    <style>
        body { font-family: Arial; background:#0d1117; color:#e6edf3; }
        h2 { color:#58a6ff; }
        table { width:100%; border-collapse: collapse; font-size:14px; }
        th, td { padding:8px; border:1px solid #30363d; }
        th { background:#161b22; }
        tr:nth-child(even){ background:#161b22; }
        .slow { color:#ff7b72; }
    </style>
</head>
<body>

<h2>🚀 MySQL Slow Query Dashboard</h2>

<table>
<tr>
    <th>Query (Normalized)</th>
    <th>Count</th>
    <th>Total Time (s)</th>
    <th>Avg Time (s)</th>
    <th>Max Time (s)</th>
</tr>

<?php foreach (array_slice($queries,0,20) as $q): ?>
<tr>
    <td><?= htmlspecialchars(substr($q['query'],0,150)) ?></td>
    <td><?= $q['count'] ?></td>
    <td class="slow"><?= round($q['total_time'],2) ?></td>
    <td><?= round($q['total_time']/$q['count'],2) ?></td>
    <td><?= round($q['max_time'],2) ?></td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
