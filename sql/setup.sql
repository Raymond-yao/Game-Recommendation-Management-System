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
  landingPage CHAR(10),
  CHECK (length(password) >= 3 AND length(username) >= 3)
);

DELIMITER //
CREATE TRIGGER trig_user_check 
BEFORE INSERT ON `Users` 
FOR EACH ROW
BEGIN
  IF (LENGTH(NEW.password) < 3 OR LENGTH(NEW.username) < 3)
  THEN 
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'username or password too short';
  END IF;
END //

CREATE TRIGGER update_user_check 
BEFORE UPDATE ON `Users` 
FOR EACH ROW
BEGIN
  IF (LENGTH(NEW.password) < 3 OR LENGTH(NEW.username) < 3)
  THEN 
  SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'username or password too short';
  END IF;
END //
DELIMITER ;

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
  viewCount INTEGER,
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
  FOREIGN KEY (gameID ) REFERENCES Games(id) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (listID ) REFERENCES RecommendationLists(id) ON UPDATE CASCADE ON DELETE CASCADE
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


INSERT INTO `users` VALUES (1, 'raymond', '123', 3, 3, "raymond@god.com", "overview");
INSERT INTO `users` VALUES (2, 'dante', '123', 3, 2, "dante@god.com", "overview");
INSERT INTO `users` VALUES (3, 'ann', '123', 2, 1, "ann@god.com", "overview");
INSERT INTO `users` VALUES (4, 'robin', '123', 3, 1, "robin@god.com", "overview");
INSERT INTO `users` VALUES (5, 'root', '123', 0, 0, "root@god.com", "overview");


INSERT INTO `friends` VALUES (1, 2);
INSERT INTO `friends` VALUES (1, 3);
INSERT INTO `friends` VALUES (1, 4);
INSERT INTO `friends` VALUES (2, 1);
INSERT INTO `friends` VALUES (2, 3);
INSERT INTO `friends` VALUES (3, 1);
INSERT INTO `friends` VALUES (4, 1);

INSERT INTO `RecommendationLists` VALUES (1, 'Steam best games', 'Here are some games which I think is the best in Steam', DATE("2018-5-31"), 1, 17);
INSERT INTO `RecommendationLists` VALUES (2, 'Best indie games', 'Best indie games I ever play!', DATE("2018-5-31"), 1, 22);
INSERT INTO `RecommendationLists` VALUES (3, '2017 Summer Sales recommendations', 'Here are 10 games which I think is the best in Steam', DATE("2018-5-31"), 2, 30);
INSERT INTO `RecommendationLists` VALUES (4, 'EA Games collection', 'Best games EA has made ever', DATE("2018-5-31"), 3, 99);
INSERT INTO `RecommendationLists` VALUES (5, 'Ubisoft Potato', "Ubisoft's server is shitty, but it did make some really cool games!", DATE("2018-5-31"), 4, 10);
INSERT INTO `RecommendationLists` VALUES (6, '2016 Winter Sales recommendations', 'What Game should you play in 2016 winter!', DATE("2018-5-31"), 4, 20);
INSERT INTO `RecommendationLists` VALUES (7, 'The best survival games on PC', 'Fighting to stay alive is hard-coded into our DNA ', DATE("2018-5-04"), 3, 25);
INSERT INTO `RecommendationLists` VALUES (8, 'The best VR games', 'Our favorite HTC Vive and Oculus Rift games right now.', DATE("2018-4-04"), 2, 50);
INSERT INTO `RecommendationLists` VALUES (9, 'The best 4X games on PC', 'The 4X genre is better than its ever been', DATE("2018-4-03"), 1, 40);
INSERT INTO `RecommendationLists` VALUES (10, 'Experience fishing in games', 'Fishing in games should be a relaxing activity, but also an exciting one.', DATE("2018-3-02"), 2, 55);
INSERT INTO `RecommendationLists` VALUES (11, 'New Nintendo Switch Releases', 'There are some popular games final release on Switch', DATE("2018-6-18"), 4, 60);





INSERT INTO `Games` VALUES(1, "Tom Clancy's Rainbow Six Siege", DATE("2015-12-1"), "Ubisoft", 9, "https://rainbow6.ubisoft.com/siege/en-ca/home/");
INSERT INTO `Games` VALUES(2, "Assassin's Creed: Origins", DATE("2017-10-27"), "Ubisoft", 8.5, "https://store.steampowered.com/app/582160/Assassins_Creed_Origins/");
INSERT INTO `Games` VALUES(3, "Assassin's Creed IV: Black Flag", DATE("2013-10-29"), "Ubisoft", 9, "https://store.steampowered.com/app/242050/Assassins_Creed_IV_Black_Flag/");
INSERT INTO `Games` VALUES(4, "Rain World", DATE("2017-3-28"), "Adult Swim Games", 9, "https://rainworldgame.com/");
INSERT INTO `Games` VALUES(5, "Hollow Knight", DATE("2017-2-1"), "Team Cherry", 9, "https://http://hollowknight.com/");
INSERT INTO `Games` VALUES(6, "Battlefield 1", DATE("2016-10-21"), "Electronic Arts", 9, "https://www.battlefield.com/");
INSERT INTO `Games` VALUES(7, "Need for Speed Payback", DATE("2017-6-2"), "Electronic Arts", 9, "https://www.ea.com/en-gb/games/need-for-speed/need-for-speed-payback");
INSERT INTO `Games` VALUES(8, "Dark Souls III", DATE("2011-9-22"), "FromSoftware, Bandai Namco Entertainment", 9, "https://store.steampowered.com/app/374320/DARK_SOULS_III/");
INSERT INTO `Games` VALUES(9, "Frostpunk", DATE("2018-4-24"), "11bit Studio", 8.8, "http://www.frostpunkgame.com/");
INSERT INTO `Games` VALUES(10, "The Long Dark", DATE("2017-8-1"),"Hinterland Studio Inc",8,"https://hinterlandgames.com/");
INSERT INTO `Games` VALUES(11, "Subnautica", DATE("2018-1-23"),"Unknown Worlds Entertainment",7,"https://unknownworlds.com/subnautica/");
INSERT INTO `Games` VALUES(12,"Don't Starve", DATE("2013-4-13"),"Klei EntertainmenT",8.5,"https://www.kleientertainment.com/games/dont-starve");
INSERT INTO `Games` VALUES(13,"Superhot VR", DATE("2017-3-7"),"Superhot Team",7.5,"https://www.oculus.com/experiences/rift/1012593518800648/");
INSERT INTO `Games` VALUES(14,"Skyrim VR", DATE("2018-4-3"),"Bethesda",7.8,"https://store.steampowered.com/agecheck/app/611670/");
INSERT INTO `Games` VALUES(15,"Keep Talking and Nobody Explodes", DATE("2014-1-23"),"Steel Crate Games",8,"https://www.humblebundle.com/store/keep-talking-and-nobody-explodes?partner=pcgamer");
INSERT INTO `Games` VALUES(16,"Dominions 5", DATE("2017-11-27"),"Illwinter Game Design",7.5,"https://store.steampowered.com/app/722060/Dominions_5__Warriors_of_the_Faith/");
INSERT INTO `Games` VALUES(17,"Sid Meiers Civilization 5", DATE("2010-9-21"),"Firaxis Games",8.8,"https://store.steampowered.com/app/8930/Sid_Meiers_Civilization_V/");
INSERT INTO `Games` VALUES(18,"Endless Space 2", DATE("2017-5-18"),"AMPLITUDE Studios",7.8,"https://store.steampowered.com/app/392110/Endless_Space_2/");
INSERT INTO `Games` VALUES(19,"Far cry 5", DATE("2018-3-26"),"Ubisoft Montreal",5.6,"https://store.steampowered.com/app/552520/Far_Cry_5/");
INSERT INTO `Games` VALUES(20,"Stardew Valley", DATE("2016-2-16"),"ConcernedApe",9.4,"https://store.steampowered.com/app/413150/Stardew_Valley/");
INSERT INTO `Games` VALUES(21,"The Sims 4", DATE("2014-9-1"),"Electronic Arts",8,"https://www.origin.com/can/en-us/store/the-sims/the-sims-4#store-page-section-overview");




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

INSERT INTO `Recommend` VALUES(7, 9);
INSERT INTO `Recommend` VALUES(7, 10);
INSERT INTO `Recommend` VALUES(7, 11);
INSERT INTO `Recommend` VALUES(7, 12);

INSERT INTO `Recommend` VALUES(8, 13);
INSERT INTO `Recommend` VALUES(8, 14);
INSERT INTO `Recommend` VALUES(8, 15);

INSERT INTO `Recommend` VALUES(9, 16);
INSERT INTO `Recommend` VALUES(9, 17);
INSERT INTO `Recommend` VALUES(9, 18);

INSERT INTO `Recommend` VALUES(10, 19);
INSERT INTO `Recommend` VALUES(10, 20);
INSERT INTO `Recommend` VALUES(10, 21);

INSERT INTO `Recommend` VALUES(11, 8);
INSERT INTO `Recommend` VALUES(11, 14);
INSERT INTO `Recommend` VALUES(11, 20);







INSERT INTO `RecommendationReasons` VALUES(1, "Hard Core startegy FPS", 1, 1);
INSERT INTO `RecommendationReasons` VALUES(2, "I love Egypt!", 1, 2);
INSERT INTO `RecommendationReasons` VALUES(3, "I love being both a pirate and an Assassin", 1, 3);
INSERT INTO `RecommendationReasons` VALUES(4, "I love its graphic effect", 1, 4);
INSERT INTO `RecommendationReasons` VALUES(5, "love its story", 1, 5);
INSERT INTO `RecommendationReasons` VALUES(7, "love suffering", 1, 8);
INSERT INTO `RecommendationReasons` VALUES(8, "I love its graphic effect", 2, 4);
INSERT INTO `RecommendationReasons` VALUES(9, "good music, epic story ... from there I read a history of the honour bugs' kindom", 2, 5);
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
INSERT INTO `RecommendationReasons` VALUES(22, "It's a harsh and beautiful survival game that confronts you with difficult choices at every turn", 7, 9);
INSERT INTO `RecommendationReasons` VALUES(23, "it's just you fighting against the elements, the wildlife, and your own human fragility.", 7, 10);
INSERT INTO `RecommendationReasons` VALUES(24, "It's hard not to compare it to Minecraft, yet developer Unknown Worlds has put their own unique stamp on the survival genre.", 7, 11);
INSERT INTO `RecommendationReasons` VALUES(25, "The standalone expansion Don't Starve Together even lets you play with pals.", 7, 12);
INSERT INTO `RecommendationReasons` VALUES(26, "It's possible to toss a brick at a man, knock his semi-auto pistol into the air, catch it, and bash him over the head with it before shooting three other men out of a helicopter behind you", 8, 13);
INSERT INTO `RecommendationReasons` VALUES(27, "Even if you've played hundreds of hours of Skyrim, the world will feel fresh, new, and wondrous again in VR.", 8, 14);
INSERT INTO `RecommendationReasons` VALUES(28, "As an action game its completely over the top, and tons of fun", 8, 15);
INSERT INTO `RecommendationReasons` VALUES(29, "Turn by turn you muster armies, recruit wizards to research apocalyptic magic spells, and fend off the attentions of other pretender gods.", 9, 16);
INSERT INTO `RecommendationReasons` VALUES(30, "Taking one of an armload of civilizations from the ancient to the modern age while competing for various victory conditions, this is the series that has championed the genre for years.", 9, 17);
INSERT INTO `RecommendationReasons` VALUES(31, "Endless Space 2 builds on some of the best ideas of its predecessor, this time crafting more unique story content for each of the distinct interstellar empires.", 9, 18);
INSERT INTO `RecommendationReasons` VALUES(32, "Catching fish is easy and satisfying, but any meditative properties are gutted by Far Cry 5s insistence on entertaining you.", 10, 19);
INSERT INTO `RecommendationReasons` VALUES(33, "It's frustratingly difficult at first the bar is tiny, the fish flail wildly, and the control scheme is unconventional. But level up your fishing skill enough to make you attrctive.", 10, 20);
INSERT INTO `RecommendationReasons` VALUES(34, "Your personal needs begins stacking up, relationship meters begin appearing, and the zen experience quickly becomes lost amidst a clouds of distracting icons.", 10, 21);
INSERT INTO `RecommendationReasons` VALUES(35, "Suffer another time", 11, 8);
INSERT INTO `RecommendationReasons` VALUES(36, "A little bit over price", 11, 14);
INSERT INTO `RecommendationReasons` VALUES(37, "The excellent game it's always fun whenever and wherever you play it.", 11, 20);









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
INSERT INTO `Images` VALUES (24, 'bf1', 'jpg');
INSERT INTO `Images` VALUES (25, 'needforspeed', 'jpg');
INSERT INTO `Images` VALUES (26, 'survive', 'png');
INSERT INTO `Images` VALUES (27, 'Dont_Starve', 'jpg');
INSERT INTO `Images` VALUES (28, 'Frostpunk', 'jpg');
INSERT INTO `Images` VALUES (29, 'Subnautica', 'png');
INSERT INTO `Images` VALUES (30, 'The-Long-Dark', 'png');
INSERT INTO `Images` VALUES (31, 'super-mario-odyssey', 'jpg');
INSERT INTO `Images` VALUES (32, 'VR', 'jpg');
INSERT INTO `Images` VALUES (33, 'Superhot_VR', 'png');
INSERT INTO `Images` VALUES (34, 'Skyrim_VR', 'png');
INSERT INTO `Images` VALUES (35, 'Steel_Crate_Games', 'jpg');
INSERT INTO `Images` VALUES (36, '4X', 'jpg');
INSERT INTO `Images` VALUES (37, 'Dominions_5', 'jpg');
INSERT INTO `Images` VALUES (38, 'Sid_Meiers_Civilization_5', 'jpg');
INSERT INTO `Images` VALUES (39, 'Endless_pace_2', 'jpg');
INSERT INTO `Images` VALUES (40, 'fishing', 'jpg');
INSERT INTO `Images` VALUES (41, 'Far_Cry_5', 'png');
INSERT INTO `Images` VALUES (42, 'Stardew_Valley', 'jpg');
INSERT INTO `Images` VALUES (43, 'The_Sims_4', 'png');









INSERT INTO `ListCovers` VALUES(3, 2);
INSERT INTO `ListCovers` VALUES(1, 4);
INSERT INTO `ListCovers` VALUES(2, 5);
INSERT INTO `ListCovers` VALUES(26, 7);
INSERT INTO `ListCovers` VALUES(32, 8);
INSERT INTO `ListCovers` VALUES(36, 9);
INSERT INTO `ListCovers` VALUES(40, 10);
INSERT INTO `ListCovers` VALUES(31, 11);





INSERT INTO `GameCovers` VALUES(21, 1);
INSERT INTO `GameCovers` VALUES(22, 2);
INSERT INTO `GameCovers` VALUES(23, 3);
INSERT INTO `GameCovers` VALUES(20, 4);
INSERT INTO `GameCovers` VALUES(3, 5);
INSERT INTO `GameCovers` VALUES(8, 8);
INSERT INTO `GameCovers` VALUES(24, 6);
INSERT INTO `GameCovers` VALUES(25, 7);
INSERT INTO `GameCovers` VALUES(27, 9);
INSERT INTO `GameCovers` VALUES(28, 10);
INSERT INTO `GameCovers` VALUES(29, 11);
INSERT INTO `GameCovers` VALUES(30, 12);
INSERT INTO `GameCovers` VALUES(33, 13);
INSERT INTO `GameCovers` VALUES(34, 14);
INSERT INTO `GameCovers` VALUES(35, 15);
INSERT INTO `GameCovers` VALUES(37, 16);
INSERT INTO `GameCovers` VALUES(38, 17);
INSERT INTO `GameCovers` VALUES(39, 18);
INSERT INTO `GameCovers` VALUES(41, 19);
INSERT INTO `GameCovers` VALUES(42, 20);
INSERT INTO `GameCovers` VALUES(43, 21);







INSERT INTO `UserImages` VALUES(9, 1, "avatar");
INSERT INTO `UserImages` VALUES(8, 1, "background");
INSERT INTO `UserImages` VALUES(10, 2, "avatar");
INSERT INTO `UserImages` VALUES(5, 2, "background");
INSERT INTO `UserImages` VALUES(31, 3, "background");


