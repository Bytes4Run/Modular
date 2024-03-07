<?php
/**
 * Messenger
 * @description This class is used to prepare and send messages to the user
 * @category Class
 * @author Jorge Echeverria <jecheverria@bytes4run.com>
 * @package Kernel\helpers\Messenger
 * @version 1.0.0
 * @date 2024-03-06
 * @time 15:00:00
 * @copyright (c) 2024 Bytes4Run
 */

declare (strict_types = 1);

namespace B4R\Kernel\helpers;

class Messenger
{
    public function __construct()
    {
        // Constructor
    }
    public function build(string $type = 'info', string | array $data = '')
    {
        return match ($type) {
            "info" => $this->buildInfoMessage($data),
            "error" => $this->buildErrorMessage($data),
            "success" => $this->buildSuccessMessage($data),
            "warning" => $this->buildWarningMessage($data),
            default => $this->buildAMessage($data),
        };
    }
    private function buildInfoMessage(string | array $data)
    {
        return $this->buildAMessage([
            'typo' => "Information",
            'message' => (isset($data['message'])) ? $data['message'] : "This is an information message",
            'code' => (isset($data['code'])) ? $data['code'] : "200",
            'title' => [
                'color' => "info",
                'icon' => "info",
                'text' => "Information",
                'background' => "info",
                'subtitle' => "",
            ],
            'extra' => (isset($data['extra'])) ? $data['extra'] : "",
            'footer' => [
                'color' => "info",
                'text' => "Bytes4Run",
                'icon' => "info",
            ],
            'image' => (isset($data['image'])) ? $data['image'] : "",
        ]);
    }

    private function buildErrorMessage(string | array $data)
    {
        return $this->buildAMessage([
            'typo' => "Execution error!",
            'message' => (isset($data['message'])) ? $data['message'] : "An error occurred during the execution of the process",
            'code' => (isset($data['code'])) ? $data['code'] : "500",
            'title' => [
                'color' => "danger",
                'icon' => "ban",
                'text' => "Error",
                'background' => "danger",
                'subtitle' => "",
            ],
            'extra' => (isset($data['extra'])) ? $data['extra'] : "",
            'footer' => [
                'color' => "danger",
                'text' => "Bytes4Run",
                'icon' => "ban",
            ],
            'image' => (isset($data['image'])) ? $data['image'] : "",
        ]);
    }

    private function buildSuccessMessage(string | array $data)
    {
        return $this->buildAMessage([
            'typo' => "Success",
            'message' => (isset($data['message'])) ? $data['message'] : "The process was executed successfully",
            'code' => (isset($data['code'])) ? $data['code'] : "200",
            'title' => [
                'color' => "success",
                'icon' => "check",
                'text' => "Success",
                'background' => "success",
                'subtitle' => "",
            ],
            'extra' => (isset($data['extra'])) ? $data['extra'] : "",
            'footer' => [
                'color' => "success",
                'text' => "Bytes4Run",
                'icon' => "check",
            ],
            'image' => (isset($data['image'])) ? $data['image'] : "",
        ]);
    }

    private function buildWarningMessage(string | array $data)
    {
        return $this->buildAMessage([
            'typo' => "Warning",
            'message' => (isset($data['message'])) ? $data['message'] : "This is a warning message",
            'code' => (isset($data['code'])) ? $data['code'] : "200",
            'title' => [
                'color' => "warning",
                'icon' => "exclamation",
                'text' => "Warning",
                'background' => "warning",
                'subtitle' => "",
            ],
            'extra' => (isset($data['extra'])) ? $data['extra'] : "",
            'footer' => [
                'color' => "warning",
                'text' => "Bytes4Run",
                'icon' => "exclamation",
            ],
            'image' => (isset($data['image'])) ? $data['image'] : "",
        ]);
    }

    private function buildAMessage(string | array $data)
    {
        $extra = "";
        if (isset($data['extra']) && is_array($data['extra'])) {
            foreach ($data['extra'] as $i => $v) {
                if (!is_array($v)) {
                    $extra .= "<em>$i : </em> $v<br>";
                } else {
                    $extra .= "<em>$i : </em><br>&nbsp;&nbsp;&nbsp;&nbsp;";
                    foreach ($v as $in => $val) {
                        if (!is_array($val)) {
                            $extra .= "<em>$in : </em> $val<br>";
                        } else {
                            $extra .= "<em>$in : </em><br>&nbsp;&nbsp;&nbsp;&nbsp;";
                            $extra .= print_r($val, true);
                        }
                    }
                }
            }
        } else {
            if (isset($data['extra']) && !is_null($data['extra']) && !empty($data['extra'])) {
                $extra .= $data['extra'];
            }

        }
        $response = [
            'header' => [
                'title' => [
                    'color' => $data['title']['Color'],
                    'text' => $data['title']['text'],
                ],
                'icon' => $data['title']['icon'],
                'background' => $data['title']['background'],
            ],
            'body' => [
                'title' => $data['typo'],
                'mensaje' => $data['message'],
                'extra' => $data['extra'],
            ],
            'footer' => [
                'text' => $data['footer']['text'],
                'color' => $data['footer']['color'],
                'icon' => $data['footer']['icon'],
            ],
        ];
        return $response;

    }
}
