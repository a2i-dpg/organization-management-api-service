<?php

if (!function_exists("clientUrl")) {
    function clientUrl($type)
    {
        if (!in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
            if ($type == "CORE") {
                return config("nise3.is_dev_mode") ? config("httpclientendpoint.core.dev") : config("httpclientendpoint.core.prod");
            } elseif ($type == "ORGANIZATION") {
                return config("nise3.is_dev_mode") ? config("httpclientendpoint.organization.dev") : config("httpclientendpoint.organization.prod");
//                return config("nise3.is_dev_mode") ? $config["organization"]["dev"] : $config["organization"]["prod"];
            } elseif ($type == "INSTITUTE") {
                return config("nise3.is_dev_mode") ? config("httpclientendpoint.institute.dev") : config("httpclientendpoint.institute.prod");
//                return config("nise3.is_dev_mode") ? $config["institute"]["dev"] : $config["institute"]["prod"];
            } elseif ($type == "IDP_SERVER") {
                return config("nise3.is_dev_mode") ? config("httpclientendpoint.idp_server.dev") : config("httpclientendpoint.idp_server.prod");
//                return config("nise3.is_dev_mode") ? $config["idp_server"]["dev"] : $config["idp_server"]["prod"];
            }

        } else {
            if ($type == "CORE") {
                return config("httpclientendpoint.core.local");
//                return $config["core"]["local"];
            } elseif ($type == "ORGANIZATION") {
                return config("httpclientendpoint.organization.local");
//                return $config["organization"]["local"];
            } elseif ($type == "INSTITUTE") {
                return config("httpclientendpoint.institute.local");
//                return $config["institute"]["local"];
            } elseif ($type == "IDP_SERVER") {
                config("nise3.is_dev_mode") ? config("httpclientendpoint.idp_server.dev") : config("httpclientendpoint.idp_server.prod");
//                return config("nise3.is_dev_mode") ? $config["idp_server"]["dev"] : $config["idp_server"]["prod"];
            }
        }
        return "";
    }
}
