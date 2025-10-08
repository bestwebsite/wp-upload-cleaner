<?php
class WPUC_Admin {
    public function __construct() { add_action('admin_menu', [$this,'menu']); }
    public function menu() {
        add_management_page(__('Upload Cleaner','wp-upload-cleaner'),__('Upload Cleaner','wp-upload-cleaner'),'manage_options','wp-upload-cleaner',[$this,'render']);
    }
    public function render() {
        if (!current_user_can('manage_options')) return;
        $msg = '';
        if (isset($_POST['wpuc_scan']) && check_admin_referer('wpuc_scan_action')) { $scan = WPUC_Core::scan(); $msg = '<div class="updated"><p>Scan complete. Orphans: '.intval($scan['counts']['orphans']).'</p></div>'; }
        if (isset($_POST['wpuc_delete_selected']) && check_admin_referer('wpuc_delete_selected_action')) {
            $files = isset($_POST['wpuc_files']) ? array_map('sanitize_text_field', (array)$_POST['wpuc_files']) : [];
            $res = WPUC_Core::delete_files($files); $msg = '<div class="updated"><p>Deleted '.count($res['deleted']).' files. Failed '.count($res['failed']).'.</p></div>';
        }
        if (isset($_POST['wpuc_delete_all']) && check_admin_referer('wpuc_delete_all_action')) {
            $scan = get_transient('wpuc_last_scan'); $files = is_array($scan)?($scan['orphans']??[]):[];
            $res = WPUC_Core::delete_files($files); $msg = '<div class="updated"><p>Deleted '.count($res['deleted']).' files. Failed '.count($res['failed']).'.</p></div>';
        }
        $scan = get_transient('wpuc_last_scan');
        echo '<div class="wrap"><h1>WP Upload Cleaner</h1><p>Find and safely remove orphaned files from <code>wp-content/uploads</code>.</p>';
        if ($msg) echo $msg;
        echo '<form method="post" style="margin-bottom:1em;">'; wp_nonce_field('wpuc_scan_action'); echo '<button class="button button-primary" name="wpuc_scan">Scan uploads</button></form>';
        if ($scan && !empty($scan['orphans'])) {
            echo '<h2>Results</h2><p><strong>Total:</strong> '.intval($scan['counts']['total']).' &nbsp; <strong>Orphans:</strong> '.intval($scan['counts']['orphans']).'</p>';
            echo '<form method="post">'; wp_nonce_field('wpuc_delete_selected_action');
            echo '<table class="widefat striped"><thead><tr><th style="width:24px;"><input type="checkbox" id="wpuc-checkall" onclick="jQuery(\'.wpuc-file\').prop(\'checked\', this.checked);" /></th><th>File</th></tr></thead><tbody>';
            $max = 500; $i = 0; foreach ($scan['orphans'] as $rel) { $rel_e = esc_html($rel); echo "<tr><td><input class='wpuc-file' type='checkbox' name='wpuc_files[]' value='{$rel_e}' /></td><td><code>{$rel_e}</code></td></tr>"; $i++; if ($i >= $max) break; }
            if (count($scan['orphans']) > $max) { $rest = count($scan['orphans']) - $max; echo "<tr><td></td><td><em>+ {$rest} more files not shown.</em></td></tr>"; }
            echo '</tbody></table><p><button class="button button-secondary" name="wpuc_delete_selected">Delete selected</button></p></form>';
            echo '<form method="post" onsubmit="return confirm(\'Delete ALL orphaned files from the last scan?\');">'; wp_nonce_field('wpuc_delete_all_action'); echo '<button class="button button-link-delete" name="wpuc_delete_all">Delete ALL found</button></form>';
        } elseif ($scan) { echo '<p><strong>No orphaned files found in the last scan.</strong></p>'; }
        echo '</div>';
    }
}
new WPUC_Admin();
