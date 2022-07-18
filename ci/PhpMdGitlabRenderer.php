<?php

namespace CI;

use PHPMD\AbstractRenderer;
use PHPMD\Report;
use PHPMD\RuleViolation;

/**
 * This class will render a GitLab compatible JSON report.
 */
class PhpMdGitlabRenderer extends AbstractRenderer
{
    /**
     * {@inheritDoc}
     */
    public function renderReport(Report $report)
    {
        $data = $this->addViolationsToReport($report);
        $data = $this->addErrorsToReport($report, $data);
        $jsonData = $this->encodeReport($data);

        $writer = $this->getWriter();
        $writer->write($jsonData . PHP_EOL);
    }


    /**
     * Add violations, if any, to GitLab Code Quality report format
     *
     * @param Report $report The report with potential violations.
     *
     * @return array The report output with violations, if any.
     */
    protected function addViolationsToReport(Report $report)
    {
        $data = [];

        /** @var RuleViolation $violation */
        foreach ($report->getRuleViolations() as $violation) {
            $path = str_replace(realpath(__DIR__ . '/..'), '', $violation->getFileName());

            $violationResult = [
                'type' => 'issue',
                'categories' =>
                [
                    'Style',
                    'PHP',
                ],
                'check_name' => $violation->getRule()->getName(),
                'fingerprint' => $path . ':' . $violation->getBeginLine() . ':' . $violation->getRule()->getName(),
                'description' => $violation->getDescription(),
                'severity' => 'minor',
                'location' =>
                [
                    'path' => $path,
                    'lines' =>
                    [
                        'begin' => $violation->getBeginLine(),
                        'end' => $violation->getEndLine(),
                    ],
                ],
            ];

            $data[] = $violationResult;
        }

        return $data;
    }

    /**
     * Add errors, if any, to GitLab Code Quality report format
     *
     * @param Report $report The report with potential errors.
     * @param array  $data   The report output to add the errors to.
     *
     * @return array The report output with errors, if any.
     */
    protected function addErrorsToReport(Report $report, array $data)
    {
        $errors = $report->getErrors();
        if ($errors) {
            foreach ($errors as $error) {
                $path = str_replace(realpath(__DIR__ . '/..'), '', $error->getFile());

                $errorResult = [
                    'description' => $error->getMessage(),
                    'fingerprint' => $path . ':0:MajorErrorInFile',
                    'severity' => 'major',
                    'location' =>
                    [
                        'path' => $path,
                        'lines' =>
                        [
                            'begin' => 0,
                        ],
                    ],
                ];

                $data[] = $errorResult;
            }
        }

        return $data;
    }

    /**
     * Encode report data to the JSON representation string
     *
     * @param array $data The report data
     *
     * @return string
     */
    private function encodeReport($data)
    {
        $encodeOptions = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP |
            (defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0);

        return json_encode($data, $encodeOptions);
    }
}
