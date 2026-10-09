<?php
defined('ABSPATH') || exit;

// WordPress's native update screen pulls tested releases from this public repo.
// No GitHub token or hosting password is required for the installed theme.
add_filter('update_themes_github.com', function ($update, $theme_data, $stylesheet, $locales) {
    $repo = 'https://github.com/juna79/MA-Khan-Website';
    if (($theme_data['UpdateURI'] ?? '') !== $repo) {
        return $update;
    }
    $release = get_transient('makhan_github_release');
    if (false === $release) {
        $response = wp_remote_get('https://api.github.com/repos/juna79/MA-Khan-Website/releases/latest', array(
            'timeout' => 10,
            'limit_response_size' => 64000,
            'headers' => array('Accept' => 'application/vnd.github+json', 'User-Agent' => 'M-A-Khan-WordPress-Theme'),
        ));
        if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
            return $update;
        }
        $release = json_decode(wp_remote_retrieve_body($response), true);
        if (!is_array($release) || !empty($release['draft']) || !empty($release['prerelease'])) {
            return $update;
        }
        set_transient('makhan_github_release', $release, HOUR_IN_SECONDS);
    }
    $version = ltrim($release['tag_name'] ?? '', 'v');
    if (!preg_match('/^\d+\.\d+\.\d+$/', $version)) {
        return $update;
    }
    $expected = $repo . '/releases/download/v' . $version . '/makhan-theme.zip';
    foreach (($release['assets'] ?? array()) as $asset) {
        if (($asset['name'] ?? '') === 'makhan-theme.zip' && ($asset['browser_download_url'] ?? '') === $expected) {
            return array(
                'id' => $repo,
                'theme' => $stylesheet,
                'version' => $version,
                'url' => $repo,
                'package' => $expected,
                'requires_php' => '7.4',
                'autoupdate' => false,
            );
        }
    }
    return $update;
}, 10, 4);
