<?php
/**
 * @author Sahahdat Hossain <raselsha@gmail.com>
 * @license mage-people.com
 * @var 1.0.0
 */

if( ! defined('ABSPATH') ) die;

if( ! class_exists('MPWEM_Timeline_Details')){
    class MPWEM_Timeline_Details{
        
        public function __construct() {
            add_action('mep_admin_event_details_before_tab_name_rich_text', [$this, 'timeline_tab']);
            add_action('mp_event_all_in_tab_item', [$this, 'timeline_tab_content']);

            add_action('admin_enqueue_scripts',  [$this, 'custom_editor_enqueue']);
            // save timeline data
            add_action('wp_ajax_mep_timeline_data_save', [$this, 'save_timeline_data_settings']);
            add_action('wp_ajax_nopriv_mep_timeline_data_save', [$this, 'save_timeline_data_settings']);
            
            // update timeline data
            add_action('wp_ajax_mep_timeline_data_update', [$this, 'timeline_data_update']);
            add_action('wp_ajax_nopriv_mep_timeline_data_update', [$this, 'timeline_data_update']);
            
            // mep_delete_timeline_data
            add_action('wp_ajax_mep_timeline_delete_item', [$this, 'timeline_delete_item']);
            add_action('wp_ajax_nopriv_mep_timeline_delete_item', [$this, 'timeline_delete_item']);
        }

        public function custom_editor_enqueue() {
            // Enqueue necessary scripts
            wp_enqueue_script('jquery');
            wp_enqueue_script('editor');
            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');
            wp_enqueue_style('thickbox');
        }
        
        public function timeline_tab(){
            ?>
            <li data-target-tabs="#mep_event_timeline_meta">
                <i class="fas fa-newspaper"></i><?php esc_html_e('Timeline Details', 'mage-eventpress'); ?>
            </li>
            <?php
        }
        
        public function timeline_tab_content($post_id) {
            ?>
            <div class="mp_tab_item" data-tab-item="#mep_event_timeline_meta">
                
                <h3><?php esc_html_e('timeline Settings', 'mage-eventpress'); ?></h3>
                <p><?php esc_html_e('timeline Settings will be here.', 'mage-eventpress'); ?></p>
                
                <section class="bg-light">
                    <h2><?php esc_html_e('timeline Settings', 'mage-eventpress'); ?></h2>
                    <span><?php esc_html_e('timeline Settings', 'mage-eventpress'); ?></span>
                </section>

                <section class="mep-timeline-section">
                    <div class="mep-timeline-items mB">
                        <?php 
                            $this->show_timeline_data($post_id);
                        ?>
                    </div>
                    <button class="button mep-timeline-item-new" data-modal="mep-timeline-item-new" type="button"><?php _e('Add timeline','mage-eventpress'); ?></button>
                </section>
                <!-- sidebar collapse open -->
                <div class="mep-modal-container" data-modal-target="mep-timeline-item-new">
                    <div class="mep-modal-content">
                        <span class="mep-modal-close"><i class="fas fa-times"></i></span>
                        <div class="title">
                            <h3><?php _e('Add F.A.Q.','mage-eventpress'); ?></h3>
                            <div id="mep-timeline-msg"></div>
                        </div>
                        <div class="content">
                            <label>
                                <?php _e('Add Title','mage-eventpress'); ?>
                                <input type="hidden" name="mep_post_id" value="<?php echo $post_id; ?>"> 
                                <input type="text"   name="mep_timeline_title"> 
                                <input type="hidden" name="mep_timeline_item_id">
                            </label>
                            <label>
                                <?php _e('Add Content','mage-eventpress'); ?>
                            </label>
                            <?php 
                                $content = ''; 
                                $editor_id = 'mep_timeline_content';
                                $settings = array(
                                    'textarea_name' => 'mep_timeline_content',
                                    'media_buttons' => true,
                                    'textarea_rows' => 10,
                                );
                                wp_editor( $content, $editor_id, $settings );
                            ?>
                            <div class="mT"></div>
                            <div class="mep_timeline_save_buttons">
                                <p><button id="mep_timeline_save" class="button button-primary button-large"><?php _e('Save','mage-eventpress'); ?></button> <button id="mep_timeline_save_close" class="button button-primary button-large">save close</button><p>
                            </div>
                            <div class="mep_timeline_update_buttons" style="display: none;">
                                <p><button id="mep_timeline_update" class="button button-primary button-large"><?php _e('Update and Close','mage-eventpress'); ?></button><p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        public function show_timeline_data($post_id){
            $mep_timeline = get_post_meta($post_id,'mep_event_day',true);
            if( ! empty($mep_timeline)):
                foreach ($mep_timeline as $key => $value) : 
                    ?>
                        <div class="mep-timeline-item" data-id="<?php echo esc_attr($key); ?>">
                            <section class="timeline-header" data-collapse-target="#timeline-content-<?php echo esc_attr($key); ?>">
                                <label class="label">
                                    <p><?php echo esc_html($value['mep_day_title']); ?></p>
                                    <div class="timeline-action">
                                        <span class="" ><i class="fas fa-eye"></i></span>
                                        <span class="mep-timeline-item-edit" data-modal="mep-timeline-item-new" ><i class="fas fa-edit"></i></span>
                                        <span class="mep-timeline-item-delete"><i class="fas fa-trash"></i></span>
                                    </div>
                                </label>
                            </section>
                            <section class="timeline-content" data-collapse="#timeline-content-<?php echo esc_attr($key); ?>">
                                <?php echo wpautop(wp_kses_post($value['mep_day_content'])); ?>
                            </section>
                        </div>
                    <?php
                endforeach;
            endif;
        }

        public function timeline_data_update() {
            $post_id = intval($_POST['mep_timeline_postID']);
            $mep_timeline = get_post_meta($post_id, 'mep_event_day', true);
            $mep_timeline = is_array($mep_timeline) ? $mep_timeline : [];
            $new_data = [
                'mep_day_title' => sanitize_text_field($_POST['mep_timeline_title']),
                'mep_day_content' => wp_kses_post($_POST['mep_timeline_content'])
            ];
            if( ! empty($mep_timeline)){
                if(isset($_POST['mep_timeline_itemID'])){
                    $mep_timeline[$_POST['mep_timeline_itemID']]=$new_data;
                }
            }
            update_post_meta($post_id, 'mep_event_day', $mep_timeline);
            ob_start();
            $resultMessage = __('Data Updated Successfully', 'mptbm_plugin_pro');
            $this->show_timeline_data($post_id);
            $html_output = ob_get_clean();
            wp_send_json_success([
                'message' => $resultMessage,
                'html' => $html_output,
            ]);
            die;
        }

        public function save_timeline_data_settings() {
            $post_id = intval($_POST['mep_timeline_postID']);
            $mep_timeline = get_post_meta($post_id, 'mep_event_day', true);
            $mep_timeline = is_array($mep_timeline) ? $mep_timeline : [];
            $new_data = [
                'mep_day_title' => sanitize_text_field($_POST['mep_timeline_title']),
                'mep_day_content' => wp_kses_post($_POST['mep_timeline_content'])
            ];
            array_push($mep_timeline,$new_data);
            $result = update_post_meta($post_id, 'mep_event_day', $mep_timeline);
            if($result){
                ob_start();
                $resultMessage = __('Data Added Successfully', 'mptbm_plugin_pro');
                $this->show_timeline_data($post_id);
                $html_output = ob_get_clean();
                wp_send_json_success([
                    'message' => $resultMessage,
                    'html' => $html_output,
                ]);
            }
            else{
                wp_send_json_success([
                    'message' => 'Data not inserted',
                    'html' => 'error',
                ]);
            }
            die;
        }

        public function timeline_delete_item(){
            $post_id = intval($_POST['mep_timeline_postID']);
            $mep_timeline = get_post_meta($post_id,'mep_event_day',true);
            $mep_timeline =  is_array($mep_timeline) ? $mep_timeline : [];
            if( ! empty($mep_timeline)){
                if(isset($_POST['itemId'])){
                    unset($mep_timeline[$_POST['itemId']]);
                    $mep_timeline = array_values($mep_timeline);
                }
            }
            $result = update_post_meta($post_id, 'mep_event_day', $mep_timeline);
            if($result){
                ob_start();
                $resultMessage = __('Data Deleted Successfully', 'mptbm_plugin_pro');
                $this->show_timeline_data($post_id);
                $html_output = ob_get_clean();
                wp_send_json_success([
                    'message' => $resultMessage,
                    'html' => $html_output,
                ]);
            }
            else{
                wp_send_json_success([
                    'message' => 'Data not inserted',
                    'html' => '',
                ]);
            }
            die;
        }
    }
    new MPWEM_Timeline_Details();
}