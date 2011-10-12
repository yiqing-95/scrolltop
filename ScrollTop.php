<?php

class ScrollTop extends CWidget {

    private $id = 'ScrollTop';
    public $label = '^top';
    public $speed = 'slow';
    public static $counter;

    public function init() {
        ScrollTop::$counter++;
        Yii::app()->getClientScript()->registerScript($this->id + '_' + ScrollTop::$counter, '
                $(function() {
                    $("#' . ($this->id + '_' + ScrollTop::$counter) . '").click(function() {
                        $("html,body").animate({ scrollTop : 0 }, "' . ($this->speed) . '");
                        return false;
                    });
                });');
        echo '<a href="#" id="' . ($this->id + '_' + ScrollTop::$counter) . '">' . ($this->label) . '</a>';
    }

}