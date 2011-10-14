<?php
/**
 * Created by JetBrains PhpStorm.
 * User:  sensorario && yiqing 
 * Date: 11-10-13
 * Time: pm 12:07
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
              
        $this->id .= '_'.self::$counter++;

        Yii::app()->getClientScript()->registerCoreScript('jquery')
        ->registerScript(__CLASS__.'#'. $this->id , '
                $(function() {
                    $("#' .$this->id. '").click(function() {
                        $("html,body").animate({ scrollTop : 0 }, "' . ($this->speed) . '");
                        return false;
                    });
                });');

        echo CHtml::link($this->label,  '#', CMap::mergeArray(array('id' => $this->id), $this->linkOptions));

    }

}

//===========================================================================

/**
 * Created by JetBrains PhpStorm.
 * User: yiqing
 * Date: 11-10-13
 * Time: 下午9:05
 * To change this template use File | Settings | File Templates.
 */

class KScrollToWidget extends CWidget
{

    /**
     * @var string
     */
    protected $_id ;
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
     * @var array
     * this is the default css settings 
     */
    protected $defaultCssSettings = array(
        'display' => ' none',
        'z-index' => ' 999',
        'opacity' => ' .8',
        'position' => ' fixed',
        'top' => ' 100%',
        'margin-top' => ' -80px',
        'right' => ' 0%',
        'margin-left' => ' -160px',
        '-moz-border-radius' => ' 24px',
        '-webkit-border-radius' => ' 24px',
        'width' => ' 300px',
        'line-height' => ' 48px',
        'height' => ' 48px',
        'padding' => ' 10px',
        'background-color' => '  #44ff88',
        'font-size' => ' 24px',
        'text-align' => ' center',
        'color' => ' #fff',
    );

    protected $rightFloat = array(
        'right' => '0%',
    );
    protected $centerFloat = array(
        'left' => '50%',
    );
    protected $leftFloat = array(
        'left' => '13%',
        'right' => false ,
        'color'=> '#445566',
    );

    /**
     * @var string  right | center | left
     * the position of the element displayed
     */
    public $position = 'right';

    /**
     * @var array|string
     * -------------------------------------------
     * if you give a string settings which will be converted to
     * an array first , so better to give an array;
     * ---------------------------------------------
     * you can add more css settings
     * and remove some default settings by
     * set it to false
     * ---------------------------------------------
     */
    public $cssSettings = array(

    );


    /**
     * @return void
     */
    public function init()
    {
        $this->publishAssets();
        $this->registerClientScripts();
        
        echo CHtml::link($this->label, '#', CMap::mergeArray(array('id' => $this->getId()), $this->linkOptions));
    }


    /**
     * @return string
     * override the parent's getId method ,some time the default implements
     * will cause some bug ( id will like yw_1 , yw_2 , this will conflict with some another widget ),
     * you can see the source code of CWidget .
     */
    public function getId(){
        //following code make the id will be generated only once in same widget
        if(isset($this->_id)){
            return $this->_id;
        }
       return $this->_id =  __CLASS__.'__'.self::$counter++;
    }

    /**
     * @return KScrollToWidget
     * if  there is  some assets folder to  publish  your can implements this method
     * this is just for future using 
     */
    public function publishAssets()
    {
        return $this;
    }


    public function registerClientScripts()
    {
        // register the css code
        $this->handleCss();
        //handle the js logic  options  pass to the plugin 
         $options = array(
            'speed'=> $this->speed,
        );
        $jsonOptions = CJavaScript::encode($options);
        // register the jquery and   plugin code
        Yii::app()->getClientScript()->registerCoreScript('jquery')
        ->registerScript(__CLASS__.'_plugin',$this->jqPluginCode(),CClientScript::POS_END)
             ->registerScript(__CLASS__ . '#' . $this->getId(),
                              " $(\"#{$this->getId()}\").scroll2top({$jsonOptions});",
                             CClientScript::POS_READY
             );

        return $this;
    }

    protected function handleCss(){
        $cssSettings =  $this->cssSettings;
        if(is_string($cssSettings)){
           $cssSettings = $this->getArrayFromCssString($cssSettings);
        }
        if(is_array($cssSettings)){

             //position handle
            if( ($position = strtolower($this->position))!== 'right'){
                if(in_array($position,array('right','left','center'))){
                    $positionCssArray = $position.'Float';
                    $this->cssSettings = CMap::mergeArray($this->cssSettings,$this->$positionCssArray);
                }else{
                    throw new CException('position should be right , left or center , you give is '.$this->position);
                }
            }
            
           $this->cssSettings = CMap::mergeArray($this->defaultCssSettings,$cssSettings);

            //iterate to handle delete css key
            foreach($this->cssSettings as $k=>$v){
                if($v === false){
                    unset($this->cssSettings[$k]);
                }
            }
        }
        $cssCode =  '#'.$this->getId(). $this->genCssFromArray($this->cssSettings);
        Yii::app()->getClientScript()->registerCss(__CLASS__.'#'.$this->getId(),$cssCode);
    }


    /**
     * @return string
     * jquery  scroll2top  plugin code
     * @see http://briancray.com/2009/10/06/scroll-to-top-link-jquery-css/
     * usage :
     *
     *  $(function() {
     *    $("#message a").scroll2top({speed : 800 });
     *    });
     *
     */
    public function jqPluginCode()
    {
        $pluginJs = ';(function($) {
                            $.fn.scroll2top = function(options) {
                                   return this.each(function() {
                                       var opts  = $.extend({}, $.fn.scroll2top.defaults, options);
                                     /*
                                    if (options) {
                                       var opts  = $.extend({}, $.fn.scroll2top.defaults, options);
                                    }else{
                                       var opts = $.fn.scroll2top.defaults ;
                                    }*/
                                    var scroll_timer;
                                    var displayed = false;
                                    var $message = $(this);
                                    var $window = $(window);
                                    var top = $(document.body).children(0).position().top;
                                    $window.scroll(function () {
                                        window.clearTimeout(scroll_timer);
                                        scroll_timer = window.setTimeout(function () {
                                            if ($window.scrollTop() <= top) {
                                                displayed = false;
                                                $message.fadeOut(500);
                                            }
                                            else if (displayed == false) {
                                                displayed = true;
                                                $message.stop(true, true).show().click(function (e) {
                                                     $message.fadeOut(500);
                                                     $("html,body").animate({scrollTop:0}, opts.speed);//800 ms is also ok !
                                                    e.preventDefault();
                                                    return false;
                                                });
                                            }
                                        }, 100);
                                    });

                                });
                            };

                            // plugin defaults
                            $.fn.scroll2top.defaults = {
                               speed : "slow"
                            };
                        })(jQuery);
                    ';
        return $pluginJs;
    }

    /**
     * @param array $cssSettings
     * @param bool $withCurlyBrace 是否带上大括号返回
     * @return string
     * 根据数组生成css设置代码
     */
    public function genCssFromArray($cssSettings = array(), $withCurlyBrace = true)
    {
        $cssCodes = '';
        foreach ($cssSettings as $k => $v) {
            $cssCodes .= "{$k}:{$v}; \n";
        }
        if ($withCurlyBrace === true) {
            $cssCodes = '{' . "\n" . $cssCodes . '}';
        }
        return $cssCodes;
    }

    /**
     * @param string $cssString
     * @return array
     * 从css代码 生成array 需要取掉两边的空格带大括号  如果有的话
     * 需要去除代码中的注释 找个工具方法或者网上搜索
     */
    public function getArrayFromCssString($cssString = '')
    {
        $rtn = array();
        //remove  {   and  }  if exists
        $cssString = rtrim(trim($cssString), '}');
        $cssString = ltrim($cssString, '{');
        //remove  all comments and space
        $text = preg_replace('!/\*.*?\*/!s', '', $cssString);
        $text = preg_replace('/\n\s*\n/', "", $text);
        // pairs handle
        $pairs = explode(';', $text);
        foreach ($pairs as $pair) {
            $colonPos = strpos($pair, ':');
            if (($k = trim(substr($pair, 0, $colonPos))) !== '') {
                $rtn[$k] = substr($pair, $colonPos + 1);
            }
        }
        return $rtn;
    }
}
