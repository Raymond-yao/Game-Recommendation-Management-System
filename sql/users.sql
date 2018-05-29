DROP TABLE IF EXISTS `friends`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  id INTEGER PRIMARY KEY,
  username VARCHAR(255),
  password VARCHAR(100),
  avatar VARCHAR(255),
  cover VARCHAR(255),
  listCount INTEGER,
  friendCount INTEGER,
  email VARCHAR(255),
  landingPage CHAR(10)
);

INSERT INTO `users` VALUES (1, 'raymond', '123', "/assets/image/dark_soul_ava", "/assets/image/dark_soul_hero", 7, 3, "raymond@god.com", "overview");
INSERT INTO `users` VALUES (2, 'dante', '123', "/assets/image/ela_avatar", "/assets/image/ela", 7, 1, "dante@god.com", "overview");
INSERT INTO `users` VALUES (3, 'ann', '123', NULL, NULL, 7, 1, "ann@god.com", "overview");
INSERT INTO `users` VALUES (4, 'robin', '123', NULL, NULL, 7, 1, "robin@god.com", "overview");


CREATE TABLE `friends` (
  followerId INTEGER,
  followeeId INTEGER,
  PRIMARY KEY (followerId, followeeId),
  FOREIGN KEY (followerId ) REFERENCES users(id) ON UPDATE CASCADE,
  FOREIGN KEY (followeeId ) REFERENCES users(id) ON UPDATE CASCADE
);

INSERT INTO `friends` VALUES (1, 2);
INSERT INTO `friends` VALUES (1, 3);
INSERT INTO `friends` VALUES (1, 4);
INSERT INTO `friends` VALUES (2, 1);
INSERT INTO `friends` VALUES (3, 1);
INSERT INTO `friends` VALUES (4, 1);




