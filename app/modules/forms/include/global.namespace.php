<?php

namespace form;

function get_path($datas = array()){
	return \dims::getInstance()->getScriptEnv()."?".http_build_query($datas);
}
