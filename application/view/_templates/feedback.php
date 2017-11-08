<?php

// get the feedback (they are arrays, to make multiple positive/negative messages possible)
$feedback_positive = Session::get('feedback_positive');
$feedback_negative = Session::get('feedback_negative');
$debug_log = Session::get('_debug_');

// echo out positive messages
if (isset($feedback_positive)) {
    foreach ($feedback_positive as $feedback) {
        echo '<div class="feedback success">'.$feedback.'</div>';
    }
}

// echo out negative messages
if (isset($feedback_negative)) {
    foreach ($feedback_negative as $feedback) {
        echo '<div class="feedback error">'.$feedback.'</div>';
    }
}

// echo out debug info messages
if (isset($debug_log)) {
    foreach ($debug_log as $message) {
        echo '<div class="feedback info">'.$message.'</div>';
    }
}
