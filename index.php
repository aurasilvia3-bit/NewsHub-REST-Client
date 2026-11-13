<?php
declare(strict_types=1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/news_api.php';

$keyword = trim((string)($_GET['q'] ?? ''));
$error = '';
$articles = [];
$mode = $keyword !== '' ? 'search' : 'top';

try {
    $result = $mode === 'search'
        ? search_news($keyword)
        : get_top_headlines();

    if (isset($result['error'])) {
        $error = $result['error'];
    } else {
        $articles = $result['articles'] ?? [];
    }
} catch (Throwable $e) {
    // Do not expose secrets or server details to visitors.
    $error = 'Terjadi kesalahan saat mengambil berita. Periksa konfigurasi API dan koneksi internet.';
    error_log($e->getMessage());
}

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function display_date(?string $date): string {
    if (!$date) return '-';
    try {
        return (new DateTime($date))->format('d M Y, H:i');
    } catch (Throwable $e) {
        return $date;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NewsHub — REST Client</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="brand" href="index.php"><span class="brand-mark">N</span> NewsHub</a>
        <span class="header-tag">PHP cURL · Public API</span>
    </div>
</header>

<main class="container">
    <section class="hero">
        <p class="eyebrow">NEWS REST CLIENT</p>
        <h1>Berita terbaru,<br><span>dalam satu tempat.</span></h1>
        <p class="hero-copy">Aplikasi REST Client sederhana menggunakan PHP cURL dan NewsAPI.</p>
        <form class="search-form" method="get" action="index.php">
            <label class="sr-only" for="q">Cari berita</label>
            <input id="q" name="q" type="search" placeholder="Cari topik berita, misalnya teknologi..." value="<?= e($keyword) ?>">
            <button type="submit">Cari berita</button>
            <?php if ($keyword !== ''): ?>
                <a class="reset-link" href="index.php">Reset</a>
            <?php endif; ?>
        </form>
    </section>

    <section class="news-section">
        <div class="section-heading">
            <div>
                <p class="eyebrow"><?= $keyword !== '' ? 'HASIL PENCARIAN' : 'UPDATE TERKINI' ?></p>
                <h2><?= $keyword !== '' ? 'Berita: ' . e($keyword) : 'Berita utama' ?></h2>
            </div>
            <span class="result-count"><?= count($articles) ?> artikel</span>
        </div>

        <?php if ($error !== ''): ?>
            <div class="notice notice-error">
                <strong>Berita belum dapat dimuat.</strong>
                <p><?= e($error) ?></p>
                <?php if (!NEWS_API_KEY || NEWS_API_KEY === 'MASUKKAN_API_KEY_ANDA'): ?>
                    <p>Tambahkan API Key NewsAPI pada file <code>.env</code>. Lihat petunjuk di README.md.</p>
                <?php endif; ?>
            </div>
        <?php elseif (count($articles) === 0): ?>
            <div class="notice">Tidak ada artikel yang ditemukan. Coba kata kunci lain.</div>
        <?php else: ?>
            <div class="news-grid">
                <?php foreach ($articles as $article):
                    $title = (string)($article['title'] ?? 'Tanpa judul');
                    $url = (string)($article['url'] ?? '');
                    $image = (string)($article['urlToImage'] ?? '');
                    $source = (string)($article['source']['name'] ?? 'Sumber tidak diketahui');
                    $description = (string)($article['description'] ?? 'Tidak ada ringkasan berita.');
                    $published = (string)($article['publishedAt'] ?? '');
                ?>
                    <article class="news-card">
                        <?php if ($image !== '' && filter_var($image, FILTER_VALIDATE_URL)): ?>
                            <img class="news-image" src="<?= e($image) ?>" alt="" loading="lazy"
                                 onerror="this.style.display='none'">
                        <?php else: ?>
                            <div class="image-placeholder">NEWS</div>
                        <?php endif; ?>
                        <div class="card-content">
                            <div class="meta"><span><?= e($source) ?></span><span><?= e(display_date($published)) ?></span></div>
                            <h3><?= e($title) ?></h3>
                            <p><?= e($description) ?></p>
                            <?php if ($url !== '' && filter_var($url, FILTER_VALIDATE_URL)): ?>
                                <a class="read-more" href="<?= e($url) ?>" target="_blank" rel="noopener noreferrer">Baca selengkapnya <span>↗</span></a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<footer class="site-footer">
    <div class="container">NewsHub REST Client · Dibuat dengan PHP, cURL, dan NewsAPI</div>
</footer>
</body>
</html>
