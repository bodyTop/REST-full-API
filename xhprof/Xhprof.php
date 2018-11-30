<?php
class Xhprof{

    public function begin(){
        xhprof_enable();
    }

    public function end(){
        $xhprof_data = xhprof_disable();

        // display raw xhprof data for the profiler run
        $XHPROF_ROOT = realpath(dirname(__FILE__));
        include_once $XHPROF_ROOT. "/xhprof_lib/utils/xhprof_lib.php";
        include_once $XHPROF_ROOT. "/xhprof_lib/utils/xhprof_runs.php";

        // save raw data for this profiler run using default
        // implementation of iXHProfRuns.
        $xhprof_runs = new XHProfRuns_Default();

        // save the run under a namespace "xhprof_foo"
        $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_foo");

        $str = $_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/'.basename(dirname(__FILE__),'/').'/xhprof_html';

        echo "---------------\n".
            "Assuming you have set up the http based UI for \n".
            "XHProf at some address, you can view run at \n".
            "<a href='http://$str/index.php?run=$run_id&source=xhprof_foo' target='_blank'>http://$str/index.php?run=$run_id&source=xhprof_foo</a> \n".
            "---------------\n";

    }
}

