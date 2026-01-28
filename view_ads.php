<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav>
        <h1>MyIntern</h1>
        <a href="view_ads.php" class="active"><i class="fas fa-search"></i> Cari Kerja</a>
        <a href="my_applications.php"><i class="fas fa-file-alt"></i> Status Saya</a>
        <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
    </nav>

    <div class="main-content">
        <div class="header-section">
            <h2>Available Positions</h2>
        </div>
        
        <div class="card-grid">
            <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <div class="card">
                    <h3><?php echo $row['title']; ?></h3>
                    <span class="company-name"><i class="fas fa-building"></i> <?php echo $row['company_name']; ?></span>
                    <p style="font-size: 14px; color: #666; margin-bottom: 20px;"><?php echo substr($row['description'], 0, 100); ?>...</p>
                    <a href="apply_process.php?ad_id=<?php echo $row['ad_id']; ?>" class="btn-apply">Mohon Sekarang</a>
                </div>
            <?php } ?>
        </div>
    </div>
</body>