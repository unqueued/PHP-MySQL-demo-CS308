/*
	Test Data for CS 308, Fall 2013

	No guarantees are made for accuracy.
	This data does NOT cover all possible cases.
	It is up to you to extend this data to fit all use cases you can imagine.
	If you find any errors or typos, please email sanford@nyu.edu
*/

INSERT INTO `person` (`pid`, `passwd`, `fname`, `lname`, `d_privacy`) VALUES
('AA', md5('AA'), 'Ann', 'Andrews', 1),
('AZ', md5('AZ'), 'Alex', 'Zhang', 1),
('BB', md5('BB'), 'Bob', 'Baker', 1),
('CC', md5('CC'), 'Cathy', 'Chan', 1),
('DD', md5('DD'), 'Dan', 'Done', 1),
('DM', md5('DM'), 'David', 'Manrique', 0),
('JM', md5('JM'), 'Jin', 'Memon', 0),
('JP', md5('JP'), 'John', 'Pierreponte', 1),
('LP', md5('LP'), 'Larry', 'Page', 1),
('MC', md5('MC'), 'Morgan', 'Chase', 1),
('RP', md5('RP'), 'Raspberry', 'Pineapple', 0),
('SB', md5('SB'), 'Sree', 'Bonar', 1),
('SJ', md5('SJ'), 'Steven', 'Jobs', 1),
('SM', md5('SM'), 'Steve', 'Mobs', 1),
('AS', md5('AS'), 'Alexander', 'Scott', 1);


INSERT INTO `event` (`eid`, `start_time`, `duration`, `description`, `pid`) VALUES
(1, '13:00:00', '02:00:00', 'Dan''s meeting in the street', 'DD'),
(2, '14:00:00', '01:00:00', 'Bob''s party on the street', 'BB'),
(3, '15:20:44', '00:01:00', 'Street attack', 'DD'),
(7, '20:00:00', '00:01:00', 'Dinner alone in the street', 'AZ'),
(8, '22:00:00', '00:30:00', 'Street time', 'AA'),
(10, '00:00:00', '00:30:00', 'Break Time', 'RP'),
(11, '00:00:00', '23:59:59', 'Study for CS 239 UNIX System Programming', 'SM'),
(14, '12:00:00', '06:00:00', 'Street Fair', 'DD'),
(15, '03:00:00', '03:00:00', 'Street View Interview!', 'DD');


INSERT INTO `eventdate` (`eid`, `edate`) VALUES
(1, '2013-10-07'),
(2, '2013-10-14'),
(3, '2013-10-15'),
(7, '2013-10-20'),
(8, '2013-10-14'),
(8, '2013-10-15'),
(10, '2013-10-22'),
(11, '2013-10-24'),
(14, '2013-10-22'),
(15, '2013-12-10');


INSERT INTO `friend_of` (`sharer`, `viewer`, `level`) VALUES
('AA', 'BB', 1),
('AA', 'CC', 3),
('AA', 'DD', 1),
('AA', 'SJ', 4),
('AZ', 'BB', 4),
('BB', 'CC', 3),
('BB', 'DD', 7),
('BB', 'RP', 3),
('BB', 'SJ', 3),
('CC', 'DD', 1),
('DD', 'BB', 3),
('DD', 'CC', 5),
('DD', 'SJ', 1),
('DM', 'BB', 2),
('DM', 'SJ', 2),
('JM', 'CC', 1),
('JM', 'MC', 1),
('LP', 'SJ', 1),
('MC', 'JM', 3),
('RP', 'BB', 3),
('RP', 'SJ', 2),
('SB', 'BB', 2),
('SB', 'CC', 3),
('SJ', 'AA', 4),
('SJ', 'BB', 1),
('SJ', 'DD', 3),
('SJ', 'DM', 1),
('SJ', 'LP', 2),
('SJ', 'RP', 2),
('SJ', 'SM', 1),
('SM', 'SB', 1),
('SM', 'SJ', 2);


INSERT INTO `invited` (`pid`, `eid`, `response`, `visibility`) VALUES
('AA', 1, 1, 2),
('AZ', 7, 1, 8),
('BB', 1, 0, 4),
('BB', 8, 1, 1),
('CC', 1, 2, 0),
('CC', 2, 2, 2),
('CC', 8, 1, 2),
('DD', 1, 0, 1),
('DD', 8, 1, 4),
('RP', 2, 1, 1),
('RP', 10, 1, 1),
('SB', 11, 1, 1),
('SJ', 11, 2, 0),
('SM', 11, 1, 4),
('DD', 14, 1, 1),
('DD', 15, 1, 4);
