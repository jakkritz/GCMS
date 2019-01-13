<?php
/**
 * @filesource modules/documentation/controllers/admin/category.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Documentation\Admin\Category;

use Gcms\Gcms;
use Gcms\Login;
use Kotchasan\Html;
use Kotchasan\Http\Request;
use Kotchasan\Language;

/**
 * module=documentation-category.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * แสดงรายการหมวดหมู่.
   *
   * @param Request $request
   *
   * @return string
   */
  public function render(Request $request)
  {
    // ข้อความ title bar
    $this->title = Language::trans('{LNG_List of} {LNG_Category}');
    // เลือกเมนู
    $this->menu = 'modules';
    // อ่านข้อมูลโมดูล และ config
    $index = \Index\Adminmodule\Model::getModuleWithConfig('documentation', $request->request('mid')->toInt());
    // admin
    $login = Login::adminAccess();
    // can_config หรือ สมาชิกตัวอย่าง
    if ($index && $login && (Gcms::canConfig($login, $index, 'can_config') || !Login::notDemoMode($login))) {
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs',
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-documents">{LNG_Module}</span></li>');
      $ul->appendChild('<li><a href="{BACKURL?module=documentation-settings&mid='.$index->module_id.'}">'.ucfirst($index->module).'</a></li>');
      $ul->appendChild('<li><span>{LNG_Category}</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h2 class="icon-category">'.$this->title.'</h2>',
      ));
      // แสดงตาราง
      $section->appendChild(createClass('Documentation\Admin\Category\View')->render($request, $index));

      return $section->render();
    }
    // 404.html

    return \Index\Error\Controller::page404();
  }
}