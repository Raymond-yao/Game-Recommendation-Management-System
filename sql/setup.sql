DROP TABLE IF EXISTS `ListCovers`;
DROP TABLE IF EXISTS `GameCovers`;
DROP TABLE IF EXISTS `UserImages`;
DROP TABLE IF EXISTS `Images`;
DROP TABLE IF EXISTS `RecommendationReasons`;
DROP TABLE IF EXISTS `Recommend`;
DROP TABLE IF EXISTS `Games`;
DROP TABLE IF EXISTS `RecommendationLists`;
DROP TABLE IF EXISTS `Friends`;
DROP TABLE IF EXISTS `Users`;

CREATE TABLE `Users` (
  id INTEGER PRIMARY KEY,
  username VARCHAR(255),
  password VARCHAR(100),
  listCount INTEGER,
  friendCount INTEGER,
  email VARCHAR(255),
  landingPage CHAR(10)
);

CREATE TABLE `Friends` (
  followerId INTEGER,
  followeeId INTEGER,
  PRIMARY KEY (followerId, followeeId),
  FOREIGN KEY (followerId ) REFERENCES Users(id) ON UPDATE CASCADE,
  FOREIGN KEY (followeeId ) REFERENCES Users(id) ON UPDATE CASCADE
);

CREATE TABLE `RecommendationLists` (
  id INTEGER PRIMARY KEY,
  title CHAR(100),
  description CHAR(200),
  createdDate DATE,
  creatorID INTEGER NOT NULL,
  FOREIGN kEY (creatorID) REFERENCES Users(id) ON UPDATE CASCADE
);

CREATE TABLE `Games` (
  id INTEGER PRIMARY KEY,
  name CHAR(100),
  salesDate DATE,
  company CHAR(100),
  rate INTEGER,
  URL CHAR(100)
);


CREATE TABLE `RecommendationReasons` (
  id INTEGER PRIMARY KEY,
  content CHAR(255),
  listID INTEGER NOT NULL,
  gameID INTEGER NOT NULL,
  FOREIGN KEY (gameID ) REFERENCES Games(id) 
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (listID ) REFERENCES RecommendationLists(id) 
    ON DELETE CASCADE  
    ON UPDATE CASCADE
);


CREATE TABLE `Recommend` (
  listID INTEGER,
  gameID INTEGER,
  PRIMARY KEY (gameID, listID),
  FOREIGN KEY (gameID ) REFERENCES Games(id) ON UPDATE CASCADE,
  FOREIGN KEY (listID ) REFERENCES RecommendationLists(id) ON UPDATE CASCADE
);

CREATE TABLE `Images` (
  id INTEGER PRIMARY KEY,
  filename CHAR(100),
  type CHAR(20)
);

CREATE TABLE `ListCovers` (
  id INTEGER PRIMARY KEY,
  listID INTEGER NOT NULL UNIQUE,
  FOREIGN KEY (id ) REFERENCES Images(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (listID ) REFERENCES RecommendationLists(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE TABLE `GameCovers` (
  id INTEGER PRIMARY KEY,
  gameID INTEGER NOT NULL,
  FOREIGN KEY (id) REFERENCES Images(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (gameID ) REFERENCES Games(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);

CREATE TABLE `UserImages` (
  id INTEGER PRIMARY KEY,
  userID INTEGER NOT NULL,
  imageType CHAR(20) NOT NULL,
  FOREIGN KEY (id) REFERENCES Images(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (userID) REFERENCES Users(id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
);


INSERT INTO `users` VALUES (1, 'raymond', '123', 2, 3, "raymond@god.com", "overview");
INSERT INTO `users` VALUES (2, 'dante', '123', 1, 2, "dante@god.com", "overview");
INSERT INTO `users` VALUES (3, 'ann', '123', 1, 1, "ann@god.com", "overview");
INSERT INTO `users` VALUES (4, 'robin', '123', 2, 1, "robin@god.com", "overview");
INSERT INTO `users` VALUES (5, 'root', '123', 0, 0, "root@god.com", "overview");


INSERT INTO `friends` VALUES (1, 2);
INSERT INTO `friends` VALUES (1, 3);
INSERT INTO `friends` VALUES (1, 4);
INSERT INTO `friends` VALUES (2, 1);
INSERT INTO `friends` VALUES (2, 3);
INSERT INTO `friends` VALUES (3, 1);
INSERT INTO `friends` VALUES (4, 1);

INSERT INTO `RecommendationLists` VALUES (1, 'Steam best games', 'Here are some games which I think is the best in Steam', DATE("2018-5-31"), 1);
INSERT INTO `RecommendationLists` VALUES (2, 'Best indie games', 'Best indie games I ever play!', DATE("2018-5-31"), 1);
INSERT INTO `RecommendationLists` VALUES (3, '2017 Summer Sales recommendations', 'Here are 10 games which I think is the best in Steam', DATE("2018-5-31"), 2);
INSERT INTO `RecommendationLists` VALUES (4, 'EA Games collection', 'Best games EA has made ever', DATE("2018-5-31"), 3);
INSERT INTO `RecommendationLists` VALUES (5, 'Ubisoft Potato', "Ubisoft's server is shitty, but it did make some really cool games!", DATE("2018-5-31"), 4);
INSERT INTO `RecommendationLists` VALUES (6, '2016 Winter Sales recommendations', 'What Game should you play in 2016 winter!', DATE("2018-5-31"), 4);

INSERT INTO `Games` VALUES(1, "Tom Clancy's Rainbow Six Siege", DATE("2015-12-1"), "Ubisoft", 9, "https://rainbow6.ubisoft.com/siege/en-ca/home/");
INSERT INTO `Games` VALUES(2, "Assassin's Creed: Origins", DATE("2017-10-27"), "Ubisoft", 8.5, "https://store.steampowered.com/app/582160/Assassins_Creed_Origins/");
INSERT INTO `Games` VALUES(3, "Assassin's Creed IV: Black Flag", DATE("2013-10-29"), "Ubisoft", 9, "https://store.steampowered.com/app/242050/Assassins_Creed_IV_Black_Flag/");
INSERT INTO `Games` VALUES(4, "Rain World", DATE("2017-3-28"), "Adult Swim Games", 9, "https://rainworldgame.com/");
INSERT INTO `Games` VALUES(5, "Hollow Knight", DATE("2017-2-1"), "Team Cherry", 9, "https://http://hollowknight.com/");
INSERT INTO `Games` VALUES(6, "Battlefield 1", DATE("2016-10-21"), "Electronic Arts", 9, "https://www.battlefield.com/");
INSERT INTO `Games` VALUES(7, "Need for Speed Payback", DATE("2017-6-2"), "Electronic Arts", 9, "https://www.ea.com/en-gb/games/need-for-speed/need-for-speed-payback");
INSERT INTO `Games` VALUES(8, "Dark Souls III", DATE("2011-9-22"), "FromSoftware, Bandai Namco Entertainment", 9, "https://store.steampowered.com/app/374320/DARK_SOULS_III/");

INSERT INTO `Recommend` VALUES(1, 1);
INSERT INTO `Recommend` VALUES(1, 2);
INSERT INTO `Recommend` VALUES(1, 3);
INSERT INTO `Recommend` VALUES(1, 4);
INSERT INTO `Recommend` VALUES(1, 5);
INSERT INTO `Recommend` VALUES(1, 8);

INSERT INTO `Recommend` VALUES(2, 5);
INSERT INTO `Recommend` VALUES(2, 4);

INSERT INTO `Recommend` VALUES(3, 1);
INSERT INTO `Recommend` VALUES(3, 2);
INSERT INTO `Recommend` VALUES(3, 3);

INSERT INTO `Recommend` VALUES(4, 6);
INSERT INTO `Recommend` VALUES(4, 7);

INSERT INTO `Recommend` VALUES(5, 1);
INSERT INTO `Recommend` VALUES(5, 2);
INSERT INTO `Recommend` VALUES(5, 3);

INSERT INTO `Recommend` VALUES(6, 2);
INSERT INTO `Recommend` VALUES(6, 4);
INSERT INTO `Recommend` VALUES(6, 6);
INSERT INTO `Recommend` VALUES(6, 7);

INSERT INTO `RecommendationReasons` VALUES(1, "Hard Core startegy FPS", 1, 1);
INSERT INTO `RecommendationReasons` VALUES(2, "I love Egypt!", 1, 2);
INSERT INTO `RecommendationReasons` VALUES(3, "I love being both a pirate and an Assassin", 1, 3);
INSERT INTO `RecommendationReasons` VALUES(4, "I love its graphic effect", 1, 4);
INSERT INTO `RecommendationReasons` VALUES(5, "love its story", 1, 5);
INSERT INTO `RecommendationReasons` VALUES(7, "love suffering", 1, 8);
INSERT INTO `RecommendationReasons` VALUES(8, "I love its graphic effect", 2, 5);
INSERT INTO `RecommendationReasons` VALUES(9, "good music, epic story ... from there I read a history of the honour bugs' kindom", 2, 4);
INSERT INTO `RecommendationReasons` VALUES(10, "A better CS with more complicated siege environment", 3, 1);
INSERT INTO `RecommendationReasons` VALUES(11, "the culture of Egypt attracts me so much", 3, 2);
INSERT INTO `RecommendationReasons` VALUES(12, "An interesting combination of pirates and assassin", 3, 3);
INSERT INTO `RecommendationReasons` VALUES(13, "EA did make an effort of reshowing WW1", 4, 6);
INSERT INTO `RecommendationReasons` VALUES(14, "Unforgetable driving experience", 4, 7);
INSERT INTO `RecommendationReasons` VALUES(15, "Hard Core startegy FPS", 5, 1);
INSERT INTO `RecommendationReasons` VALUES(16, "A good combination of Egypt culture and assassin", 5, 2);
INSERT INTO `RecommendationReasons` VALUES(17, "Assassin and sea culture", 5, 3);
INSERT INTO `RecommendationReasons` VALUES(18, "Can't believe they make it to Egypt", 6, 2);
INSERT INTO `RecommendationReasons` VALUES(19, "Cool water color effect, fun game", 6, 4);
INSERT INTO `RecommendationReasons` VALUES(20, "Nice shooting feeling", 6, 6);
INSERT INTO `RecommendationReasons` VALUES(21, "I love Driving!", 6, 7);


INSERT INTO `Images` VALUES (1, 'ea', 'png');
INSERT INTO `Images` VALUES (2, 'ubisoft', 'jpeg');
INSERT INTO `Images` VALUES (3, 'hollow_knight', 'jpeg');
INSERT INTO `Images` VALUES (4, 'no_photo', 'jpeg');
INSERT INTO `Images` VALUES (5, 'ela', 'jpg');
INSERT INTO `Images` VALUES (6, 'r6', 'jpg');
INSERT INTO `Images` VALUES (7, 'rainbowsix', 'jpg');
INSERT INTO `Images` VALUES (8, 'dark_soul_hero', 'jpeg');
INSERT INTO `Images` VALUES (9, 'dark_soul_ava', 'jpeg');
INSERT INTO `Images` VALUES (10, 'ela_avatar', 'jpg');
INSERT INTO `Images` VALUES (11, 'favicon', 'png');
INSERT INTO `Images` VALUES (12, 'bg1', 'jpg');
INSERT INTO `Images` VALUES (13, 'bg2', 'jpg');
INSERT INTO `Images` VALUES (14, 'bg3', 'jpg');
INSERT INTO `Images` VALUES (15, 'bg4', 'jpg');
INSERT INTO `Images` VALUES (16, 'bg6', 'jpg');
INSERT INTO `Images` VALUES (17, 'bg5', 'jpg');
INSERT INTO `Images` VALUES (18, 'bg7', 'gif');
INSERT INTO `Images` VALUES (19, 'bg8', 'gif');
INSERT INTO `Images` VALUES (20, 'rain_world', 'jpg');
INSERT INTO `Images` VALUES (21, 'rainbow_six_siege', 'jpg');
INSERT INTO `Images` VALUES (22, 'ac_origins', 'jpg');
INSERT INTO `Images` VALUES (23, 'black_flag', 'jpg');

INSERT INTO `ListCovers` VALUES(3, 2);
INSERT INTO `ListCovers` VALUES(1, 4);
INSERT INTO `ListCovers` VALUES(2, 5);

INSERT INTO `GameCovers` VALUES(21, 1);
INSERT INTO `GameCovers` VALUES(22, 2);
INSERT INTO `GameCovers` VALUES(23, 3);
INSERT INTO `GameCovers` VALUES(20, 4);
INSERT INTO `GameCovers` VALUES(3, 5);
INSERT INTO `GameCovers` VALUES(8, 8);

INSERT INTO `UserImages` VALUES(9, 1, "avatar");
INSERT INTO `UserImages` VALUES(8, 1, "background");
INSERT INTO `UserImages` VALUES(10, 2, "avatar");
INSERT INTO `UserImages` VALUES(5, 2, "background");


