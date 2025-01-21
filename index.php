<?php
$dictionary = json_decode(file_get_contents('dictionary.json'), true);
$searchResults = [];
$selectedWord = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $searchQuery = $_POST['search'];
    $searchResults = array_filter($dictionary, function ($entry) use ($searchQuery) {
        return stripos($entry['word'], $searchQuery) !== false;
    });
}

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
                                    <input type="hidden" name="select" value="<?php echo htmlspecialchars($result['word']); ?>">
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
        <div class="right-column">
            <?php if ($selectedWord): ?>
                <h2><?php echo htmlspecialchars($selectedWord['word']); ?></h2>
                <p><?php echo htmlspecialchars($selectedWord['description']); ?></p>
                <h3>発音</h3>
                <audio controls>
                    <source src="<?php echo htmlspecialchars($selectedWord['pronunciation']); ?>" type="audio/mpeg">
                    お使いのブラウザは音声再生に対応していません。
                </audio>
                <p>発音記号: <?php echo htmlspecialchars($selectedWord['phonetic']); ?></p>
                <h3>類義語</h3>
                <ul>
                    <?php foreach ($selectedWord['synonyms'] as $synonym): ?>
                        <li><?php echo htmlspecialchars($synonym); ?>: <?php echo htmlspecialchars($selectedWord['synonym_differences'][$synonym]); ?></li>
                    <?php endforeach; ?>
                </ul>
                <h3>対義語</h3>
                <ul>
                    <?php foreach ($selectedWord['antonyms'] as $antonym): ?>
                        <li><?php echo htmlspecialchars($antonym); ?></li>
                    <?php endforeach; ?>
                </ul>
                <h3>例文</h3>
                <p><?php echo htmlspecialchars($selectedWord['example']); ?></p>
                <h3>似たようなスペルの単語</h3>
                <ul>
                    <?php foreach ($selectedWord['similar_spelling'] as $similar): ?>
                        <li><?php echo htmlspecialchars($similar); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>選択された項目がここに表示されます。</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
