<?php
/**
 * Created by JetBrains PhpStorm.
 * User: yiqing
 * Date: 11-10-13
 * Time: 下午12:07
 * To change this template use File | Settings | File Templates.
 */

class ScrollTop extends CWidget
{

    /**
     * @var string
     */
    private $id = 'ScrollTop';
    /**
     * @var string
     */
    public $label = '^top';
    /**
     * @var string
     */
    public $speed = 'slow';
    /**
     * @var int
     */
    public static $counter = 1;


    /**
     * @var array
     */
    public $linkOptions = array();


    /**
     * @return void
     */
    public function init()
    {
        self::$counter++;
        Yii::app()->getClientScript()->registerScript($this->id + '_' + ScrollTop::$counter, '
                $(function() {
                    $("#' . ($this->id + '_' + self::$counter) . '").click(function() {
                        $("html,body").animate({ scrollTop : 0 }, "' . ($this->speed) . '");
                        return false;
                    });
                });');

        echo CHtml::link($this->label, $url = '#', CMap::mergeArray(array('id' => $this->id), $this->linkOptions));

    }

}