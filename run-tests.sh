#!/bin/bash

# File paths
TEST_RESULTS="test-results.xml"
HTML_REPORT="test-report.html"

# Step 1: Run PHPUnit tests and generate test-results.xml
echo "Running PHPUnit tests..."
vendor/bin/phpunit --log-junit $TEST_RESULTS
if [ $? -ne 0 ]; then
    echo "Tests failed or encountered errors."
fi

# Step 2: Check if test-results.xml exists
if [ ! -f "$TEST_RESULTS" ]; then
    echo "Error: Test results XML file not found."
    exit 1
fi

echo "Tests completed successfully."
sleep 1

# Step 3: Generate HTML report
echo "Generating HTML report..."
php -r "
\$xmlFile = '$TEST_RESULTS';
\$htmlFile = '$HTML_REPORT';

if (!file_exists(\$xmlFile)) {
    die(\"Test results XML file not found: \$xmlFile\n\");
}

\$xml = simplexml_load_file(\$xmlFile);
if (!\$xml) {
    die(\"Failed to parse test results XML file: \$xmlFile\n\");
}

\$testcases = [];
foreach (\$xml->xpath('//testcase') as \$testcase) {
    \$name = (string)\$testcase['classname'] . '::' . (string)\$testcase['name'];
    \$status = isset(\$testcase->failure) ? 'fail' : 'pass';
    \$time = (string)\$testcase['time'];
    \$testcases[] = ['name' => \$name, 'status' => \$status, 'time' => \$time];
}

\$html = '<!DOCTYPE html>
<html>
<head>
    <title>Test Report</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .pass { color: green; font-weight: bold; }
        .fail { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Test Report</h1>
    <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
    <table>
        <thead>
            <tr>
                <th>Test Name</th>
                <th>Status</th>
                <th>Time (s)</th>
            </tr>
        </thead>
        <tbody>';

foreach (\$testcases as \$test) {
    \$html .= '
            <tr>
                <td>' . htmlspecialchars(\$test['name']) . '</td>
                <td class=\"' . htmlspecialchars(\$test['status']) . '\">' . htmlspecialchars(ucfirst(\$test['status'])) . '</td>
                <td>' . htmlspecialchars(\$test['time']) . '</td>
            </tr>';
}

\$html .= '</tbody>
    </table>
</body>
</html>';

file_put_contents(\$htmlFile, \$html);
echo \"Report generated: \$htmlFile\n\";
"

sleep 1

# Step 4: Open the HTML report in the default browser
echo "Opening HTML report in browser..."
sleep 1
open "$HTML_REPORT" || xdg-open "$HTML_REPORT"