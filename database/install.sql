DROP DATABASE IF EXISTS maf;

CREATE DATABASE maf DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

use maf;

CREATE TABLE admins(
	id int UNSIGNED NOT NULL AUTO_INCREMENT,
	mail VARCHAR(255) NOT NULL,
	password VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE news(
	id int UNSIGNED NOT NULL AUTO_INCREMENT,
	title VARCHAR(255) NOT NULL,
	content TEXT NOT NULL,
	exerpt VARCHAR(255) NOT NULL,
    published_at DATETIME NOT NULL,
    PRIMARY KEY (id)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO admins (mail,password)
VALUES
('admin@admin.com','admin');

INSERT INTO news (title,content,exerpt,published_at)
VALUES
('La MaF dans ta gueule',
'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tenetur, quod totam? Numquam excepturi facere accusantium quasi magnam cumque vel omnis dolores nobis! Eius eaque, iure. Adipisci ab est voluptatum ea.',
'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tenetur, quod totam?',
NOW()
);

INSERT INTO news (title,content,exerpt,published_at)
VALUES
('Vincent, grand DA en formation deviens exerpert comptable',
'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tenetur, quod totam? Numquam excepturi facere accusantium quasi magnam cumque vel omnis dolores nobis! Eius eaque, iure. Adipisci ab est voluptatum ea.',
'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Tenetur, quod totam?',
NOW()
);