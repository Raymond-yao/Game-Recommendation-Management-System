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
INSERT INTO `users` VALUES (1, 'raymond', '123', "/assets/image/dark_soul_ava", "/assets/image/dark_soul_hero", 7, 0, "raymond@god.com", NULL);
INSERT INTO `users` VALUES (2, 'dante', '123', "/assets/image/ela_avatar", "/assets/image/ela", 7, 0, "dante@god.com", NULL);
INSERT INTO `users` VALUES (3, 'ann', '123', NULL, NULL, 7, 0, "ann@god.com", NULL);
INSERT INTO `users` VALUES (4, 'robin', '123', NULL, NULL, 7, 0, "robin@god.com", NULL);
