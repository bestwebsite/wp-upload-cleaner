<?php
if (defined('WP_CLI') && WP_CLI) {
    class WPUC_CLI extends WP_CLI_Command {
        public function scan($args, $assoc_args) {
            $res = WPUC_Core::scan();
            $rows = array_map(function($rel){ return ['file'=>$rel]; }, $res['orphans']);
            if (empty($rows)) { WP_CLI::success('No orphaned files found.'); return; }
            $format = $assoc_args['format'] ?? 'table';
            WP_CLI\Utils\format_items($format, $rows, ['file']);
            WP_CLI::log(sprintf('Total: %d, Orphans: %d', $res['counts']['total'], $res['counts']['orphans']));
        }
        public function delete_all($args, $assoc_args) {
            $scan = get_transient('wpuc_last_scan'); $orphans = is_array($scan)?($scan['orphans']??[]):[];
            if (empty($orphans)) { WP_CLI::success('Nothing to delete. Run scan first.'); return; }
            WP_CLI\Utils\confirm('Delete ALL orphaned files found in last scan?', $assoc_args);
            $res = WPUC_Core::delete_files($orphans);
            WP_CLI::success(sprintf('Deleted %d, Failed %d', count($res['deleted']), count($res['failed'])));
        }
        public function delete($args) {
            $path = $args[0] ?? null; if (!$path) { WP_CLI::error('Provide a relative path under uploads.'); return; }
            $res = WPUC_Core::delete_files([$path]); if (!empty($res['deleted'])) WP_CLI::success('Deleted: '.$path); else WP_CLI::error('Failed to delete: '.$path);
        }
    }
    WP_CLI::add_command('upload-cleaner', 'WPUC_CLI');
}
