<?php

namespace Ozanmuyes\Paraveley\Traits;

trait FormRequestExtractor {
    protected function fromLaravelToParsley($name, $params = []) {
        // TODO MAPPH-1 Use C-style string placeholders in map
        $map = [
            // MAPPH-2 "placeholdered" => ["pattern", "%d"],
            "accepted" => ["pattern", "yes|on|1|true"],
            // "active_url" => ["pattern", ""],
            // "after" => ["", $params[0]],
            "alpha" => ["pattern" => "^[A-z]+$"],
            "alpha_dash" => ["pattern" => "^[A-z-_]+$"],
            "alpha_num" => ["type" => "alphanum"],
            // "array" => ["", ""],
            // "before" => ["", $params[0]],
            "between" => ["length", "[" . implode(",", $params) . "]"],
            "boolean" => ["pattern", "true|false|1|0"],
            // "confirmed" => ["", ""],
            // "date" => ["", ""],
            "date_format" => ["pattern", $params[0]],
            "different" => ["pattern", "(?!" . $params[0] . ").+"],
            "digits" => [["type", "digits"] , ["maxlength", $params[0]]],
            "digits_between" => [["type", "digits"] , ["length", "[" . implode(",", $params) . "]"]],
            "email" => ["type", "email"],
            // "exists" => ["", ""],
            // "image" => ["pattern", "jpeg, png, bmp, gif, or svg"],
            "in" => ["pattern", implode("|", $params)],
            "integer" => ["type", "integer"],
            "ip" => ["pattern", "\b(?:(?:2(?:[0-4][0-9]|5[0-5])|[0-1]?[0-9]?[0-9])\.){3}(?:(?:2([0-4][0-9]|5[0-5])|[0-1]?[0-9]?[0-9]))\b"],
            "max" => ["maxlength", $params[0]],
            // "mimes" => ["", $params[0] . "|" . $params[1]],
            "min" => ["minlength", $params[0]],
            "not_id" => ["pattern", "(?!" . implode("|", $params) . ").+"],
            "numeric" => ["type", "integer"],
            "regex" => ["pattern", $params[0]],
            "required" => ["required", "true"],
            // "required_with" => ["", implode("|", $params)],
            // "required_with_all" => ["", implode("|", $params)],
            // "required_without" => ["", implode("|", $params)],
            // "required_without_all" => ["", implode("|", $params)],
            // "same" => ["", ""],
            "size" => ["length", "[" . implode(",", $params) . "]"],
            "string" => ["pattern", ".+"],
            // "timezone" => ["", ""],
            // "unique" => ["", $params[0] . $params[1] . $params[2] . $params[3]],
            "url" => ["type", "url"]
        ];

        // TODO MAPPH-3 Process all placeholdered values with sprintf() and supplied $params

        // If rule name could not be found return given value back with parameters (if exists)
        if (!isset($map[$name])) {
            return [$name, is_array($params) ? implode(" ", $params) : $name];
        }

        return $map[$name];
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
    }
}