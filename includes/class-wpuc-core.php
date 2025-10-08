<?php
class WPUC_Core {
    public static function get_referenced_paths() {
        global $wpdb;
        $referenced = [];
        $ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE post_type='attachment'");
        foreach ($ids as $id) {
            $attached = get_post_meta($id, '_wp_attached_file', true);
            if ($attached) $referenced[ltrim($attached, '/\\')] = true;
            $meta = wp_get_attachment_metadata($id);
            if (is_array($meta) && !empty($meta['file'])) {
                $dir = dirname($meta['file']);
                if (!empty($meta['sizes'])) foreach ($meta['sizes'] as $sz) {
                    if (!empty($sz['file'])) $referenced[ltrim($dir . '/' . $sz['file'], '/\\')] = true;
                }
            }
        }
        return $referenced;
    }
    public static function get_all_upload_files() {
        $uploads = wp_get_upload_dir();
        $basedir = trailingslashit($uploads['basedir']);
        $all = [];
        if (!is_dir($basedir)) return $all;
        $ignore = ['index.php','.htaccess','web.config','.DS_Store'];
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($basedir, RecursiveDirectoryIterator::SKIP_DOTS));
        foreach ($it as $file) {
            if ($file->isDir()) continue;
            if (in_array($file->getBasename(), $ignore, true)) continue;
            $path = str_replace('\\','/', substr($file->getPathname(), strlen($basedir)));
            $all.append(ltrim($path, '/'));
        }
        return $all;
    }
    public static function scan() {
        $referenced = self::get_referenced_paths();
        $all_files = self::get_all_upload_files();
        $orphans = [];
        foreach ($all_files as $rel) if (!isset($referenced[$rel])) $orphans[] = $rel;
        $result = ['orphans'=>$orphans,'counts'=>['total'=>count($all_files),'referenced'=>count($all_files)-count($orphans),'orphans'=>count($orphans)],'scanned_at'=>time()];
        set_transient('wpuc_last_scan', $result, HOUR_IN_SECONDS);
        return $result;
    }
    public static function delete_files($rel_paths) {
        $uploads = wp_get_upload_dir();
        $basedir = trailingslashit($uploads['basedir']);
        $out = ['deleted'=>[],'failed'=>[]];
        foreach ((array)$rel_paths as $rel) {
            $rel = ltrim($rel, '/\\');
            $abs = realpath($basedir . $rel);
            if (!$abs || strpos($abs, realpath($basedir)) !== 0) { $out['failed'][] = $rel; continue; }
            if (is_file($abs) && @unlink($abs)) $out['deleted'][] = $rel; else $out['failed'][] = $rel;
        }
        return $out;
    }
}
