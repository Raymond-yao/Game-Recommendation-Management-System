DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  id INTEGER PRIMARY KEY,
  username VARCHAR(255),
  password VARCHAR(100),
  avatar VARCHAR(255),
  cover VARCHAR(255),
  listcount INTEGER,
  friendcount INTEGER,
  email VARCHAR(255),
  landingpage CHAR(10)
);
INSERT INTO `users` VALUES (1, 'raymond', '123', NULL, NULL, 7, 0, "raymond@god.com", NULL);
INSERT INTO `users` VALUES (2, 'dante', '123', NULL, NULL, 7, 0, "dante@god.com", NULL);
INSERT INTO `users` VALUES (3, 'ann', '123', NULL, NULL, 7, 0, "ann@god.com", NULL);
INSERT INTO `users` VALUES (4, 'robin', '123', NULL, NULL, 7, 0, "robin@god.com", NULL);
