<?php

namespace Ozanmuyes\Paraveley\Traits;

trait FormRequestExtractor {
    /**
     * Maps Laravel 5 validation rules to corresponding Parsley rules.
     *
     * Remarks
     *  - If no one-to-one matching founds, mimics the rule to get
     *    closest result (mostly via regex).
     *  - Matching order based on mostly used Laravel 5
     *    validation rules.
     *
     * @param  string $name   Laravel 5 validation rule name
     * @param  array  $params Laravel 5 validation rule parameters
     * @return array          Parsley data attribute name(s) and value(s) as associative array
     */
    protected function fromLaravelToParsley($name, $params = [])
    {
        switch ($name) {
            case "required":
                $converted = ["required", "true"];
                break;

            case "email":
                $converted = ["type", "email"];
                break;

            case "alpha":
                $converted = ["pattern" => "^[A-z]+$"];
                break;

            case "max":
                $converted = ["maxlength", $params[0]];
                break;

            case "min":
                $converted = ["minlength", $params[0]];
                break;

            case "between":
                $converted = ["length", "[" . implode(",", $params) . "]"];
                break;

            case "digits":
                $converted = [
                    ["type", "digits"],
                    ["maxlength", $params[0]]
                ];
                break;

            case "digits_between":
                $converted = [
                    ["type", "digits"],
                    ["length", "[" . implode(",", $params) . "]"]
                ];
                break;

            case "ip":
                $converted = ["pattern", "\b(?:(?:2(?:[0-4][0-9]|5[0-5])|[0-1]?[0-9]?[0-9])\.){3}(?:(?:2([0-4][0-9]|5[0-5])|[0-1]?[0-9]?[0-9]))\b"];
                break;

            case "url":
                $converted = ["type", "url"];
                break;

            case "integer":
                $converted = ["type", "integer"];
                break;

            case "boolean":
                $converted = ["pattern", "true|false|1|0"];
                break;

            case "accepted":
                $converted = ["pattern", "yes|on|1|true"];
                break;

            case "alpha_dash":
                $converted = ["pattern" => "^[A-z-_]+$"];
                break;

            case "alpha_num":
                $converted = ["type" => "alphanum"];
                break;

            case "date_format":
                $converted = ["pattern", $params[0]];
                break;

            case "different":
                $converted = ["pattern", "(?!" . $params[0] . ").+"];
                break;

            case "in":
                $converted = ["pattern", implode("|", $params)];
                break;

            case "not_id":
                $converted = ["pattern", "(?!" . implode("|", $params) . ").+"];
                break;

            case "numeric":
                $converted = ["type", "integer"];
                break;

            case "regex":
                $converted = ["pattern", $params[0]];
                break;

            case "size":
                $converted = ["length", "[" . implode(",", $params) . "]"];
                break;

            case "string":
                $converted = ["pattern", ".+"];
                break;

            default:
                $converted = [$name, is_array($params) ? implode(" ", $params) : $name];
        }

        return $converted;
    }

    public function parsleyRules()
    {
        $rulesForAttributes = $this->rules();
        $parsleyRules = [];

        foreach ($rulesForAttributes as $attribute => $rulesString) {
            /**
             * Rules string for specific attribute.
             *
             * For "title" attribute;
             * array[
             *  "required",
             *  "min:3",
             *  "max:64"
             * ]
             *
             * @var array
             */
            $rules = explode("|", $rulesString);

            foreach ($rules as $rule) {
                /**
                 * Individual rule with parameter (if exists)
                 *
                 * For "min" rule of "title" attribute;
                 * array[
                 *  0 => "min",
                 *  1 => "3"
                 * ]
                 *
                 * @var array
                 */
                $rule = explode(":", $rule);

                $laravelRuleName = $rule[0];

                if (isset($rule[1])) {
                    $laravelRuleParameters = explode(",", $rule[1]);
                } else {
                    $laravelRuleParameters = [null, null];
                }

                $parsleyRule = $this->fromLaravelToParsley($laravelRuleName, $laravelRuleParameters);

                if (is_array($parsleyRule[0])) {
                    // There is more than one validation produced to handle given Laravel rule.

                    foreach ($parsleyRule as $producedRule) {
                        $parsleyRules[$attribute]["data-parsley-" . $producedRule[0]] = $producedRule[1];
                    }
                } else {
                    $parsleyRules[$attribute]["data-parsley-" . $parsleyRule[0]] = $parsleyRule[1];
                }
            }
        }

        return $parsleyRules;
    }
}