<?php
// JSONファイルから辞書データを読み込み
$dictionary = json_decode(file_get_contents('dictionary.json'), true);
$searchResults = [];
$selectedWord = null;

// 検索処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
    $searchResults = array_filter($dictionary, function ($entry) use ($searchQuery) {
        return stripos($entry['word'], $searchQuery) !== false;
    });
}

// 選択処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select'])) {
    $selectedWord = array_filter($dictionary, function ($entry) {
        return $entry['word'] === $_POST['select'];
    });
    $selectedWord = reset($selectedWord);
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>創作文字辞書</title>
</head>
<body>
    <div class="container">
        <!-- 左カラム -->
        <div class="left-column">
            <form method="post">
                <input type="text" name="search" placeholder="検索" value="<?php echo isset($searchQuery) ? htmlspecialchars($searchQuery) : ''; ?>">
                <button type="submit">検索</button>
            </form>
            <div class="search-results">
                <?php if (!empty($searchResults)): ?>
                    <ul>
                        <?php foreach ($searchResults as $result): ?>
                            <li>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="select" value="<?php echo
  htmlspecialchars($result['word']); ?>">
                                    <button type="submit"><?php echo htmlspecialchars($result['word']); ?></button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>結果がありません。</p>
                <?php endif; ?>
            </div>
        </div>
        <!-- 右カラム -->
        <div class="right-column">
            <?php if ($selectedWord): ?>
                <h2><?php echo htmlspecialchars($selectedWord['word']); ?></h2>
                <p><?php echo htmlspecialchars($selectedWord['description']); ?></p>
            <?php else: ?>
                <p>選択された項目がここに表示されます。</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
