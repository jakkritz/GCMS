<?php
/**
 * @filesource modules/index/views/profile.php
 *
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 *
 * @see http://www.kotchasan.com/
 */

namespace Index\Profile;

use Gcms\Gcms;
use Kotchasan\Http\Request;
use Kotchasan\Language;
use Kotchasan\Mime;
use Kotchasan\Model;
use Kotchasan\Template;

/**
 * หน้าแก้ไขข้อมูลส่วนตัว.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Gcms\View
{
    /**
     * แสดงข้อมูลส่วนตัวสมาชิก
     *
     * @param Request $request
     * @param object  $index
     *
     * @return object
     */
    public function render(Request $request, $index)
    {
        // อ่านข้อมูลสมาชิก
        $model = new Model();
        $user = $model->db()->createQuery()
            ->from('user')
            ->where(array('id', (int) $_SESSION['login']['id']))
            ->first();
        $template = Template::create('member', 'member', 'profile');
        $contents = array(
            '/<NEWREGISTER>(.*)<\/NEWREGISTER>/isu' => $request->request('action')->toString() === 'newregister' ? '\\1' : '',
            '/<IDCARD>(.*)<\/IDCARD>/isu' => empty(self::$cfg->member_idcard) ? '' : '\\1',
            '/{ACCEPT}/' => Mime::getAccept(self::$cfg->user_icon_typies),
        );
        // ข้อมูลฟอร์ม
        foreach ($user as $key => $value) {
            if ($key == 'sex') {
                $datas = array();
                foreach (Language::get('SEXES') as $k => $v) {
                    $sel = $k == $value ? ' selected' : '';
                    $datas[] = '<option value="'.$k.'"'.$sel.'>'.$v.'</option>';
                }
                $contents['/{SEX}/'] = implode('', $datas);
            } elseif ($key === 'icon') {
                if (is_file(ROOT_PATH.self::$cfg->usericon_folder.$value)) {
                    $icon = WEB_URL.self::$cfg->usericon_folder.$value;
                } else {
                    $icon = WEB_URL.'skin/img/noicon.jpg';
                }
                $contents['/{ICON}/'] = $icon;
            } elseif ($key !== 'token') {
                $contents['/{'.strtoupper($key).'}/'] = $value;
            }
        }
        $template->add($contents);
        // after render
        Gcms::$view->setContentsAfter(array(
            '/:type/' => empty(self::$cfg->user_icon_typies) ? 'jpg' : implode(', ', (self::$cfg->user_icon_typies)),
        ));
        $index->detail = $template->render();
        // คืนค่า

        return $index;
    }
}
