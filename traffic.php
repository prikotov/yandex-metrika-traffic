<?php

require_once __DIR__ . '/../yandex-metrika-core/MetrikaClient.php';

MetrikaClient::checkGitignore();
$config = MetrikaClient::loadConfig();

function parseArgs(array $argv): array
{
    $result = [
        'dateFrom' => date('Y-m-d', strtotime('-30 days')),
        'dateTo' => date('Y-m-d'),
        'by' => 'source',
        'sort' => 'visits',
        'order' => 'desc',
        'limit' => null,
        'page' => null
    ];
    
    $i = 1;
    while ($i < count($argv)) {
        $arg = $argv[$i];
        
        if (in_array($arg, ['--by', '-b']) && isset($argv[$i + 1])) {
            $result['by'] = $argv[++$i];
        } elseif (in_array($arg, ['--sort', '-s']) && isset($argv[$i + 1])) {
            $result['sort'] = $argv[++$i];
        } elseif (in_array($arg, ['--order', '-o']) && isset($argv[$i + 1])) {
            $result['order'] = $argv[++$i];
        } elseif (in_array($arg, ['--limit', '-l']) && isset($argv[$i + 1])) {
            $result['limit'] = (int)$argv[++$i];
        } elseif (in_array($arg, ['--page', '-p']) && isset($argv[$i + 1])) {
            $result['page'] = $argv[++$i];
        } elseif (!str_starts_with($arg, '-') && strlen($arg) === 10 && strpos($arg, '-') !== false) {
            if (!$result['dateFrom'] || $result['dateFrom'] === date('Y-m-d', strtotime('-30 days'))) {
                $result['dateFrom'] = $arg;
            } else {
                $result['dateTo'] = $arg;
            }
        }
        $i++;
    }
    
    return $result;
}

function getDimension(string $by): string
{
    $map = [
        'source' => 'ym:s:trafficSource',
        'search_engine' => 'ym:s:searchEngine',
        'social_network' => 'ym:s:socialNetwork',
        'referral_domain' => 'ym:s:startURLDomain'
    ];
    
    return $map[$by] ?? 'ym:s:trafficSource';
}

function getDimensionLabel(string $by): string
{
    $map = [
        'source' => 'Источник трафика',
        'search_engine' => 'Поисковая система',
        'social_network' => 'Соцсеть',
        'referral_domain' => 'Реферальный домен'
    ];
    
    return $map[$by] ?? 'Источник';
}

function getSortField(string $sort): string
{
    $map = [
        'visits' => 'ym:s:visits',
        'visitors' => 'ym:s:users',
        'bounce_rate' => 'ym:s:bounceRate',
        'page_depth' => 'ym:s:pageDepth',
        'avg_duration' => 'ym:s:avgVisitDurationSeconds'
    ];
    
    return $map[$sort] ?? 'ym:s:visits';
}

$args = parseArgs($argv);

$client = new MetrikaClient(
    $config['client_id'],
    $config['client_secret'],
    $config['counter_id']
);

function getTrafficData(MetrikaClient $client, string $dateFrom, string $dateTo, string $dimension, string $sortField, string $order, ?string $pageFilter = null): array
{
    $prefix = $order === 'asc' ? '' : '-';
    
    $params = [
        'ids' => $client->getCounterId(),
        'metrics' => 'ym:s:visits,ym:s:users,ym:s:pageviews,ym:s:bounceRate,ym:s:pageDepth,ym:s:avgVisitDurationSeconds',
        'dimensions' => $dimension,
        'date1' => $dateFrom,
        'date2' => $dateTo,
        'limit' => 1000,
        'sort' => $prefix . $sortField
    ];
    
    if ($pageFilter) {
        $params['filters'] = "ym:s:startURL=@'$pageFilter'";
    }
    
    $data = $client->request($params);
    
    $result = [];
    foreach ($data['data'] ?? [] as $item) {
        $result[] = [
            'source' => $item['dimensions'][0]['name'] ?? '',
            'visits' => (int)($item['metrics'][0] ?? 0),
            'visitors' => (int)($item['metrics'][1] ?? 0),
            'pageviews' => (int)($item['metrics'][2] ?? 0),
            'bounce_rate' => round($item['metrics'][3] ?? 0, 2),
            'page_depth' => round($item['metrics'][4] ?? 0, 2),
            'avg_duration' => round($item['metrics'][5] ?? 0, 2)
        ];
    }
    
    return $result;
}

$dimension = getDimension($args['by']);
$traffic = getTrafficData($client, $args['dateFrom'], $args['dateTo'], $dimension, getSortField($args['sort']), $args['order'], $args['page']);

if ($args['limit'] !== null && $args['limit'] > 0) {
    $traffic = array_slice($traffic, 0, $args['limit']);
}

$reportPath = MetrikaClient::createReportDir();
$timestamp = MetrikaClient::getFileTimestamp();

$label = getDimensionLabel($args['by']);
$title = "Источники трафика по {$label}";
if ($args['page']) {
    $title .= " (страница: {$args['page']})";
}

echo "\n  Папка отчета: metrika_reports/" . basename($reportPath) . "\n";
echo "  Период: {$args['dateFrom']} — {$args['dateTo']}\n";
echo "  Группировка: {$label}\n";
echo "  Сортировка: {$args['sort']} ({$args['order']})\n";
if ($args['page']) {
    echo "  Фильтр по странице: {$args['page']}\n";
}
if ($args['limit'] !== null) {
    echo "  Лимит: топ {$args['limit']}\n";
}
echo "\n";

MetrikaClient::saveCsv($traffic, "$reportPath/traffic_$timestamp.csv");
MetrikaClient::saveMarkdown($traffic, "$reportPath/traffic_$timestamp.md", $title, $args['dateFrom'], $args['dateTo']);

echo "  Создано файлов:\n";
echo "    - traffic_$timestamp.csv\n";
echo "    - traffic_$timestamp.md\n";
echo "\n  Найдено записей: " . count($traffic) . "\n";
