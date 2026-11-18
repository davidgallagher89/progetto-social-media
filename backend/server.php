<?php

// Header CORS per il frontend React
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, Accept, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

header("Content-Type: application/json; charset=UTF-8");

// Connessione MySQL
$dbHost = getenv('DB_HOST') ?: 'db';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_DATABASE') ?: 'social_db';
$dbUser = getenv('DB_USERNAME') ?: 'root';
$dbPass = getenv('DB_PASSWORD') ?: 'root';

$pdo = null;
$maxRetries = 10;
$retryCount = 0;

while ($retryCount < $maxRetries) {
    try {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        break;
    } catch (\PDOException $e) {
        $retryCount++;
        sleep(1);
    }
}

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Errore di connessione al database MySQL.']);
    exit(1);
}

// Creazione tabelle se mancanti
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        avatar_url VARCHAR(500) NULL,
        cover_url VARCHAR(500) NULL,
        bio TEXT NULL,
        job_title VARCHAR(255) NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        image_url VARCHAR(500) NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_post_like (post_id, user_id),
        FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Utenti iniziali per popolare il feed
$communityUsers = [
    [
        'id' => 1,
        'name' => 'David Vella',
        'email' => 'david@example.com',
        'avatar_url' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=200&auto=format&fit=crop&q=80',
        'cover_url' => 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=1200&auto=format&fit=crop&q=80',
        'bio' => 'Full-Stack Developer. Appassionato di React, PHP e Docker.',
        'job_title' => 'Full-Stack Developer',
    ],
    [
        'id' => 2,
        'name' => 'Chiara Romano',
        'email' => 'chiara@example.com',
        'avatar_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&auto=format&fit=crop&q=80',
        'cover_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&auto=format&fit=crop&q=80',
        'bio' => 'UI/UX Designer. Minimalismo e fotografia analogica.',
        'job_title' => 'Product Designer',
    ],
    [
        'id' => 3,
        'name' => 'Marco Bianchi',
        'email' => 'marco@example.com',
        'avatar_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&auto=format&fit=crop&q=80',
        'cover_url' => 'https://images.unsplash.com/photo-1526374965328-7f61d4dc18c5?w=1200&auto=format&fit=crop&q=80',
        'bio' => 'Backend & DevOps. Container Docker tra un caffe e l\'altro.',
        'job_title' => 'DevOps Engineer',
    ],
    [
        'id' => 4,
        'name' => 'Sofia Ferri',
        'email' => 'sofia@example.com',
        'avatar_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=200&auto=format&fit=crop&q=80',
        'cover_url' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=1200&auto=format&fit=crop&q=80',
        'bio' => 'Lavoro da remoto e viaggi in giro per l\'Europa.',
        'job_title' => 'Content Creator',
    ],
    [
        'id' => 5,
        'name' => 'Luca Moretti',
        'email' => 'luca@example.com',
        'avatar_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=200&auto=format&fit=crop&q=80',
        'cover_url' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1200&auto=format&fit=crop&q=80',
        'bio' => 'Sviluppo software e automazioni.',
        'job_title' => 'Software Engineer',
    ],
    [
        'id' => 6,
        'name' => 'Valentina Rossi',
        'email' => 'valentina@example.com',
        'avatar_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=200&auto=format&fit=crop&q=80',
        'cover_url' => 'https://images.unsplash.com/photo-1497215728101-856f4ea42174?w=1200&auto=format&fit=crop&q=80',
        'bio' => 'Project Manager e appassionata di prodotti digitali.',
        'job_title' => 'Product Manager',
    ]
];

$userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
if ($userCount == 0) {
    $stmt = $pdo->prepare("
        INSERT INTO users (id, name, email, password, avatar_url, cover_url, bio, job_title, created_at)
        VALUES (:id, :name, :email, :password, :avatar_url, :cover_url, :bio, :job_title, NOW())
    ");
    foreach ($communityUsers as $user) {
        $stmt->execute([
            ':id'         => $user['id'],
            ':name'       => $user['name'],
            ':email'      => $user['email'],
            ':password'   => password_hash('password123', PASSWORD_BCRYPT),
            ':avatar_url' => $user['avatar_url'],
            ':cover_url'  => $user['cover_url'],
            ':bio'        => $user['bio'],
            ':job_title'  => $user['job_title'],
        ]);
    }
}

$samplePosts = [
    [
        'content' => 'Appena sistemato l\'ambiente con Docker e PHP 8.2, finalmente tutto parte con un solo comando.',
        'image_url' => 'https://images.unsplash.com/photo-1607799279861-4dd421887fb3?w=800&auto=format&fit=crop&q=80',
    ],
    [
        'content' => 'Sto provando l\'integrazione con le API di DeepSeek per suggerire bozze rapide nei post.',
        'image_url' => null,
    ],
    [
        'content' => 'Weekend in montagna prima di tornare a scrivere codice lunedi.',
        'image_url' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?w=800&auto=format&fit=crop&q=80',
    ],
    [
        'content' => 'Progettare bene le chiavi primarie e le foreign key in MySQL evita un sacco di problemi dopo.',
        'image_url' => null,
    ],
    [
        'content' => 'Postazione pronta per lavorare al frontend React.',
        'image_url' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=800&auto=format&fit=crop&q=80',
    ],
    [
        'content' => 'Aggiornata la dashboard oggi, molto piu veloce il caricamento dei dati.',
        'image_url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&auto=format&fit=crop&q=80',
    ],
    [
        'content' => 'Tenere separati frontend React e backend REST rende tutto piu facile da gestire.',
        'image_url' => null,
    ],
    [
        'content' => 'Un po\' di relax vista mare a fine giornata.',
        'image_url' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&auto=format&fit=crop&q=80',
    ],
];

function createSamplePost($pdo, $communityUsers, $samplePosts) {
    $otherUsers = array_filter($communityUsers, fn($u) => $u['id'] > 1);
    $randomUser = $otherUsers[array_rand($otherUsers)];
    $randomTemplate = $samplePosts[array_rand($samplePosts)];

    $stmt = $pdo->prepare("
        INSERT INTO posts (user_id, content, image_url, created_at)
        VALUES (:user_id, :content, :image_url, NOW())
    ");
    $stmt->execute([
        ':user_id'   => $randomUser['id'],
        ':content'   => $randomTemplate['content'],
        ':image_url' => $randomTemplate['image_url'],
    ]);
    $newPostId = $pdo->lastInsertId();

    $potentialLikers = [1, 2, 3, 4, 5, 6];
    shuffle($potentialLikers);
    $likers = array_slice($potentialLikers, 0, rand(1, 3));
    foreach ($likers as $likerId) {
        $stmtLike = $pdo->prepare("INSERT IGNORE INTO likes (post_id, user_id, created_at) VALUES (?, ?, NOW())");
        $stmtLike->execute([$newPostId, $likerId]);
    }

    $commentsPool = [
        "Bel lavoro!",
        "Bella foto, dove sei?",
        "Vero, il database va pensato bene fin dall'inizio.",
        "Ottimo aggiornamento!",
        "Interessante, lo provo anch'io."
    ];
    $commentUser = $otherUsers[array_rand($otherUsers)];
    $randomComment = $commentsPool[array_rand($commentsPool)];

    $stmtComment = $pdo->prepare("
        INSERT INTO comments (post_id, user_id, content, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmtComment->execute([$newPostId, $commentUser['id'], $randomComment]);
}

$postCount = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
if ($postCount == 0) {
    for ($i = 0; $i < 3; $i++) {
        createSamplePost($pdo, $communityUsers, $samplePosts);
    }
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

function getJsonBody() {
    return json_decode(file_get_contents('php://input'), true) ?: [];
}

// GET /api/posts
if ($uri === '/api/posts' && $method === 'GET') {
    if (isset($_GET['auto_generate']) && $_GET['auto_generate'] == '1') {
        createSamplePost($pdo, $communityUsers, $samplePosts);
    }

    $stmt = $pdo->query("
        SELECT 
            p.id, p.content, p.image_url, p.created_at,
            u.id as user_id, u.name as user_name, u.avatar_url as user_avatar, u.job_title as user_job,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
    ");
    $rawPosts = $stmt->fetchAll();

    $posts = [];
    foreach ($rawPosts as $rp) {
        $cStmt = $pdo->prepare("
            SELECT c.id, c.content, c.created_at, u.id as user_id, u.name, u.avatar_url
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ?
            ORDER BY c.created_at ASC
        ");
        $cStmt->execute([$rp['id']]);
        $rawComments = $cStmt->fetchAll();

        $comments = array_map(function($c) {
            return [
                'id'         => $c['id'],
                'content'    => $c['content'],
                'created_at' => $c['created_at'],
                'user'       => [
                    'id'         => $c['user_id'],
                    'name'       => $c['name'],
                    'avatar_url' => $c['avatar_url'],
                ]
            ];
        }, $rawComments);

        $posts[] = [
            'id'          => $rp['id'],
            'content'     => $rp['content'],
            'image_url'   => $rp['image_url'],
            'created_at'  => $rp['created_at'],
            'likes_count' => (int)$rp['likes_count'],
            'user'        => [
                'id'         => $rp['user_id'],
                'name'       => $rp['user_name'],
                'avatar_url' => $rp['user_avatar'],
                'job_title'  => $rp['user_job'],
            ],
            'comments'    => $comments,
        ];
    }

    echo json_encode($posts);
    exit;
}

// POST /api/posts
if ($uri === '/api/posts' && $method === 'POST') {
    $body = getJsonBody();
    $content = trim($body['content'] ?? '');
    $imageUrl = trim($body['image_url'] ?? '');

    if (empty($content)) {
        http_response_code(422);
        echo json_encode(['error' => 'Il testo del post è obbligatorio.']);
        exit;
    }

    $userId = 1;
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, image_url, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$userId, $content, $imageUrl ?: null]);
    $newId = $pdo->lastInsertId();

    $userStmt = $pdo->prepare("SELECT id, name, avatar_url, job_title FROM users WHERE id = ?");
    $userStmt->execute([$userId]);
    $user = $userStmt->fetch();

    echo json_encode([
        'message' => 'Post pubblicato con successo!',
        'post' => [
            'id'          => $newId,
            'content'     => $content,
            'image_url'   => $imageUrl ?: null,
            'created_at'  => date('Y-m-d H:i:s'),
            'likes_count' => 0,
            'user'        => $user,
            'comments'    => [],
        ]
    ], 201);
    exit;
}

// DELETE /api/posts/{id}
if (preg_match('#^/api/posts/(\d+)$#', $uri, $matches) && $method === 'DELETE') {
    $postId = $matches[1];
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    echo json_encode(['message' => 'Post eliminato con successo.']);
    exit;
}

// POST /api/likes/toggle
if ($uri === '/api/likes/toggle' && $method === 'POST') {
    $body = getJsonBody();
    $postId = (int)($body['post_id'] ?? 0);
    $userId = 1;

    $check = $pdo->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
    $check->execute([$postId, $userId]);
    $existing = $check->fetch();

    if ($existing) {
        $del = $pdo->prepare("DELETE FROM likes WHERE id = ?");
        $del->execute([$existing['id']]);
        $liked = false;
    } else {
        $ins = $pdo->prepare("INSERT INTO likes (post_id, user_id, created_at) VALUES (?, ?, NOW())");
        $ins->execute([$postId, $userId]);
        $liked = true;
    }

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
    $countStmt->execute([$postId]);
    $count = (int)$countStmt->fetchColumn();

    echo json_encode([
        'liked'       => $liked,
        'likes_count' => $count,
    ]);
    exit;
}

// POST /api/comments
if ($uri === '/api/comments' && $method === 'POST') {
    $body = getJsonBody();
    $postId = (int)($body['post_id'] ?? 0);
    $content = trim($body['content'] ?? '');
    $userId = 1;

    if (empty($content) || $postId <= 0) {
        http_response_code(422);
        echo json_encode(['error' => 'Dati commento non validi.']);
        exit;
    }

    $ins = $pdo->prepare("INSERT INTO comments (post_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())");
    $ins->execute([$postId, $userId, $content]);
    $commentId = $pdo->lastInsertId();

    $userStmt = $pdo->prepare("SELECT id, name, avatar_url FROM users WHERE id = ?");
    $userStmt->execute([$userId]);
    $user = $userStmt->fetch();

    echo json_encode([
        'message' => 'Commento aggiunto!',
        'comment' => [
            'id'         => $commentId,
            'content'    => $content,
            'created_at' => date('Y-m-d H:i:s'),
            'user'       => $user,
        ]
    ], 201);
    exit;
}

// GET /api/users/{id}
if (preg_match('#^/api/users/(\d+)$#', $uri, $matches) && $method === 'GET') {
    $userId = $matches[1];

    $uStmt = $pdo->prepare("SELECT id, name, email, avatar_url, cover_url, bio, job_title, created_at FROM users WHERE id = ?");
    $uStmt->execute([$userId]);
    $user = $uStmt->fetch();

    if (!$user) {
        http_response_code(404);
        echo json_encode(['error' => 'Utente non trovato.']);
        exit;
    }

    $postsCountStmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ?");
    $postsCountStmt->execute([$userId]);
    $user['posts_count'] = (int)$postsCountStmt->fetchColumn();

    $likesReceivedStmt = $pdo->prepare("
        SELECT COUNT(l.id) 
        FROM likes l 
        JOIN posts p ON l.post_id = p.id 
        WHERE p.user_id = ?
    ");
    $likesReceivedStmt->execute([$userId]);
    $user['likes_received'] = (int)$likesReceivedStmt->fetchColumn();

    $pStmt = $pdo->prepare("
        SELECT 
            p.id, p.content, p.image_url, p.created_at,
            (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count
        FROM posts p
        WHERE p.user_id = ?
        ORDER BY p.created_at DESC
    ");
    $pStmt->execute([$userId]);
    $rawPosts = $pStmt->fetchAll();

    $userPosts = [];
    foreach ($rawPosts as $rp) {
        $cStmt = $pdo->prepare("
            SELECT c.id, c.content, c.created_at, u.id as user_id, u.name, u.avatar_url
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ?
            ORDER BY c.created_at ASC
        ");
        $cStmt->execute([$rp['id']]);
        $comments = $cStmt->fetchAll();

        $userPosts[] = [
            'id'          => $rp['id'],
            'content'     => $rp['content'],
            'image_url'   => $rp['image_url'],
            'created_at'  => $rp['created_at'],
            'likes_count' => (int)$rp['likes_count'],
            'user'        => [
                'id'         => $user['id'],
                'name'       => $user['name'],
                'avatar_url' => $user['avatar_url'],
                'job_title'  => $user['job_title'],
            ],
            'comments'    => array_map(fn($c) => [
                'id' => $c['id'],
                'content' => $c['content'],
                'created_at' => $c['created_at'],
                'user' => ['id' => $c['user_id'], 'name' => $c['name'], 'avatar_url' => $c['avatar_url']]
            ], $comments),
        ];
    }

    echo json_encode([
        'user'  => $user,
        'posts' => $userPosts,
    ]);
    exit;
}

// GET /api/community
if ($uri === '/api/community' && $method === 'GET') {
    $stmt = $pdo->query("
        SELECT id, name, avatar_url, bio, job_title,
               (SELECT COUNT(*) FROM posts WHERE user_id = users.id) as posts_count
        FROM users 
        ORDER BY id ASC
    ");
    echo json_encode($stmt->fetchAll());
    exit;
}

// POST /api/ai/generate
if ($uri === '/api/ai/generate' && $method === 'POST') {
    $body = getJsonBody();
    $topic = trim($body['topic'] ?? '');

    $apiKey = getenv('DEEPSEEK_API_KEY');
    if (!$apiKey || $apiKey === 'inserisci_qui_la_tua_chiave_deepseek') {
        echo json_encode([
            'success' => true,
            'text'    => "Qualche aggiornamento su {$topic}! Fatemi sapere cosa ne pensate nei commenti."
        ]);
        exit;
    }

    $ch = curl_init('https://api.deepseek.com/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'model' => 'deepseek-chat',
        'messages' => [
            ['role' => 'system', 'content' => 'Sei un assistente per social media. Scrivi post brevi in italiano.'],
            ['role' => 'user', 'content' => "Scrivi un breve post su questo argomento: {$topic}"]
        ]
    ]));
    $res = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($res, true);
    $text = $json['choices'][0]['message']['content'] ?? 'Nessun testo generato.';
    echo json_encode(['success' => true, 'text' => trim($text)]);
    exit;
}

// POST /api/ai/summarize
if ($uri === '/api/ai/summarize' && $method === 'POST') {
    $body = getJsonBody();
    $postId = (int)($body['post_id'] ?? 0);

    $cStmt = $pdo->prepare("SELECT content FROM comments WHERE post_id = ?");
    $cStmt->execute([$postId]);
    $comments = $cStmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($comments)) {
        echo json_encode(['summary' => 'Nessun commento presente per questo post.']);
        exit;
    }

    $apiKey = getenv('DEEPSEEK_API_KEY');
    if ($apiKey && $apiKey !== 'inserisci_qui_la_tua_chiave_deepseek') {
        $commentsText = implode("\n- ", $comments);
        $ch = curl_init('https://api.deepseek.com/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'model' => 'deepseek-chat',
            'messages' => [
                ['role' => 'system', 'content' => 'Riassumi in 2 frasi i commenti sotto un post.'],
                ['role' => 'user', 'content' => "Commenti:\n- {$commentsText}"]
            ]
        ]));
        $res = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($res, true);
        $summary = $json['choices'][0]['message']['content'] ?? null;
        if ($summary) {
            echo json_encode(['summary' => trim($summary)]);
            exit;
        }
    }

    $summary = "Sono presenti " . count($comments) . " commenti sul post. Ultimo commento: \"" . end($comments) . "\"";
    echo json_encode(['summary' => $summary]);
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Endpoint non trovato.']);
