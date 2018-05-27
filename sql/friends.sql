DROP TABLE IF EXISTS `friends`;
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




