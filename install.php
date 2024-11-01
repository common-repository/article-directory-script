<?php
$sql_commands = array("tables"=>array(), "data"=>array());

$sql_commands['tables']['condrag_article_type'] = "CREATE TABLE IF NOT EXISTS `condrag_article_type` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";

$sql_commands['data']['condrag_article_type'] = "INSERT INTO `condrag_article_type` (`id`, `title`) VALUES
(1, 'Service'),
(2, 'Product'),
(3, 'Point of View');";

$sql_commands['tables']['condrag_article_approval_requests'] = "CREATE TABLE IF NOT EXISTS `condrag_article_approval_requests` (
  `id` int(11) NOT NULL auto_increment,
  `deny_reason` text NOT NULL,
  `do_approve` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `approved` char(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;";

$sql_commands['tables']['condrag_articles'] = "CREATE TABLE IF NOT EXISTS `condrag_articles` (
  `article_id` int(12) NOT NULL auto_increment,
  `article_state` int(11) NOT NULL default '0',
  `article_featured_state` int(11) NOT NULL default '0',
  `article_submitterid` int(12) NOT NULL default '1',
  `article_categoryid` int(12) NOT NULL default '0',
  `article_typeid` int(11) NOT NULL,
  `article_title` varchar(255) NOT NULL default '',
  `article_urltitle` varchar(255) NOT NULL default '',
  `article_summary` text NOT NULL,
  `article_keywords` text NOT NULL,
  `article_custom_fields` text NOT NULL,
  `article_text` text NOT NULL,
  `article_authorinfo` text NOT NULL,
  `article_submitteddate` datetime NOT NULL default '0000-00-00 00:00:00',
  `article_modifydate` datetime NOT NULL default '0000-00-00 00:00:00',
  `article_viewcount` int(12) NOT NULL default '0',
  `article_clicks` text NOT NULL,
  `author_firstname` varchar(255) NOT NULL,
  `author_lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(60) NOT NULL,
  `enabled` char(1) NOT NULL default '1',
	`memberinfo` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`article_id`),
  UNIQUE KEY `article_urltitle` (`article_urltitle`,`article_categoryid`),
  KEY `article_state` (`article_state`,`article_categoryid`),
  FULLTEXT KEY `article_title` (`article_title`,`article_text`,`article_keywords`)
) ENGINE=MyISAM;";

$sql_commands['tables']['condrag_categories'] = "CREATE TABLE IF NOT EXISTS `condrag_categories` (
  `category_id` int(12) NOT NULL auto_increment,
  `category_parentid` int(12) NOT NULL default '0',
  `category_title` varchar(255) NOT NULL default '',
  `category_urltitle` varchar(255) NOT NULL default '',
  `active` char(1) NOT NULL default '0',
  PRIMARY KEY  (`category_id`),
  UNIQUE KEY `category_urltitle` (`category_urltitle`,`category_parentid`),
  KEY `category_parentid` (`category_parentid`)
) ENGINE=MyISAM;";

$sql_commands['data']['condrag_categories'] = "INSERT INTO `condrag_categories` (`category_id`, `category_parentid`, `category_title`, `category_urltitle`, `active`) VALUES
(1, 0, 'Business', 'business', '0'),
(2, 0, 'Finance', 'finance', '0'),
(3, 1, 'Marketing and Advertising', 'marketing', '0'),
(4, 2, 'Insurance', 'insurance', '0'),
(5, 1, 'Advertising', 'advertising', '0'),
(6, 1, 'Branding', 'branding', '0'),
(7, 1, 'Careers Employment', 'careers-employment', '0'),
(8, 1, 'Change Management', 'change-management', '0'),
(9, 1, 'Customer Service', 'customer-service', '0'),
(10, 1, 'Entrepreneurialism', 'entrepreneurialism', '0'),
(11, 1, 'Ethics', 'ethics', '0'),
(12, 1, 'Franchising', 'franchising', '0'),
(52, 34, 'Classical Studies', 'classical-studies', '0'),
(39, 0, 'News', 'news', '0'),
(40, 0, 'Recreation', 'recreation', '0'),
(41, 0, 'Reference', 'reference', '0'),
(42, 0, 'Regional', 'regional', '0'),
(43, 0, 'Science', 'science', '0'),
(19, 0, 'Computers and Technology', 'computers-and-technology', '0'),
(47, 34, 'Photography', 'photography', '0'),
(197, 43, 'Anomalies and Alternative Science', 'anomalies-and-alternative-science', '0'),
(44, 0, 'Shopping', 'shopping', '0'),
(45, 0, 'Society', 'society', '0'),
(46, 0, 'Sports', 'sports', '0'),
(49, 34, 'Architecture', 'architecture', '0'),
(50, 34, 'Art History', 'art-history', '0'),
(51, 34, 'Bodyart', 'bodyart', '0'),
(48, 34, 'Animation', 'animation', '0'),
(223, 44, 'Children', 'children', '0'),
(34, 0, 'Arts', 'arts', '0'),
(35, 0, 'Games', 'games', '0'),
(36, 0, 'Health', 'health', '0'),
(37, 0, 'Home', 'home', '0'),
(38, 0, 'Kids and Teens', 'kids-and-teens', '0'),
(53, 34, 'Comics', 'comics', '0'),
(54, 34, 'Crafts', 'crafts', '0'),
(55, 34, 'Dance', 'dance', '0'),
(56, 34, 'Digital', 'digital', '0'),
(57, 34, 'Graphic Design', 'graphic-design', '0'),
(58, 34, 'Illustration', 'illustration', '0'),
(59, 34, 'Movies', 'movies', '0'),
(60, 34, 'Literature', 'literature', '0'),
(61, 34, 'Music', 'music', '0'),
(62, 34, 'Native and Tribal', 'native-and-tribal', '0'),
(63, 34, 'Myths and Folktales', 'myths-and-folktales', '0'),
(64, 34, 'Performing Arts', 'performing-arts', '0'),
(65, 34, 'Television', 'television', '0'),
(222, 44, 'Books', 'books', '0'),
(67, 34, 'Video', 'video', '0'),
(68, 34, 'Visual Arts', 'visual-arts', '0'),
(69, 34, 'Writers Resources', 'writers-resources', '0'),
(70, 1, 'Accounting', 'accounting', '0'),
(71, 1, 'E-Commerce', 'e-commerce', '0'),
(72, 1, 'Education and Training', 'education-and-training', '0'),
(73, 1, 'Information Services', 'information-services', '0'),
(74, 1, 'International Business and Trade', 'international-business-and-trade', '0'),
(75, 1, 'Investing', 'investing', '0'),
(76, 1, 'Management', 'management', '0'),
(77, 1, 'Opportunities', 'opportunities', '0'),
(78, 1, 'Small Business', 'small-business', '0'),
(79, 19, 'Computer Science', 'computer-science', '0'),
(80, 19, 'Hardware', 'hardware', '0'),
(81, 19, 'Internet', 'internet', '0'),
(82, 19, 'Security', 'security', '0'),
(83, 19, 'Software', 'software', '0'),
(84, 19, 'Systems', 'systems', '0'),
(85, 35, 'Board Games', 'board-games', '0'),
(86, 35, 'Card Games', 'card-games', '0'),
(87, 35, 'Coin-Op', 'coin-op', '0'),
(88, 35, 'Computer Games', 'computer-games', '0'),
(89, 35, 'Dice', 'dice', '0'),
(90, 35, 'Gambling', 'gambling', '0'),
(91, 35, 'Hand Games', 'hand-games', '0'),
(92, 35, 'Hand-Eye Coordination', 'hand-eye-coordination', '0'),
(93, 35, 'Online', 'online', '0'),
(94, 35, 'Paper and Pencil', 'paper-and-pencil', '0'),
(95, 35, 'Party Games', 'party-games', '0'),
(96, 35, 'Play-By-Mail', 'play-by-mail', '0'),
(97, 35, 'Puzzles', 'puzzles', '0'),
(98, 35, 'Roleplaying', 'roleplaying', '0'),
(99, 35, 'Tile Games', 'tile-games', '0'),
(100, 35, 'Trading Card Games', 'trading-card-games', '0'),
(101, 35, 'Video Games', 'video-games', '0'),
(102, 35, 'Yard Deck and Table Games', 'yard-deck-and-table-games', '0'),
(103, 36, 'Alternative', 'alternative', '0'),
(104, 36, 'Conditions and Diseases', 'conditions-and-diseases', '0'),
(105, 36, 'Healthcare Industry', 'healthcare-industry', '0'),
(106, 36, 'Medicine', 'medicine', '0'),
(107, 36, 'Mental Health', 'mental-health', '0'),
(108, 37, 'Apartment Living', 'apartment-living', '0'),
(109, 37, 'Consumer Information', 'consumer-information', '0'),
(110, 37, 'Cooking', 'cooking', '0'),
(111, 37, 'Do-It-Yourself', 'do-it-yourself', '0'),
(112, 37, 'Domestic Services', 'domestic-services', '0'),
(113, 37, 'Emergency Preparation', 'emergency-preparation', '0'),
(114, 37, 'Entertaining', 'entertaining', '0'),
(115, 37, 'Family', 'family', '0'),
(116, 37, 'Gardening', 'gardening', '0'),
(117, 37, 'Home Automation', 'home-automation', '0'),
(118, 37, 'Home Improvement', 'home-improvement', '0'),
(119, 37, 'Homemaking', 'homemaking', '0'),
(120, 37, 'Homeowners', 'homeowners', '0'),
(121, 37, 'Moving and Relocating', 'moving-and-relocating', '0'),
(122, 37, 'Personal Finance', 'personal-finance', '0'),
(123, 37, 'Personal Organization', 'personal-organization', '0'),
(124, 37, 'Pets', 'pets', '0'),
(125, 37, 'Rural Living', 'rural-living', '0'),
(126, 37, 'Seniors', 'seniors', '0'),
(127, 37, 'Urban Living', 'urban-living', '0'),
(128, 38, 'Pre-School', 'pre-school', '0'),
(129, 38, 'School Time', 'school-time', '0'),
(130, 38, 'Sports and Hobbies', 'sports-and-hobbies', '0'),
(131, 38, 'Teen Life', 'teen-life', '0'),
(132, 38, 'Family', 'family', '0'),
(133, 39, 'Alternative', 'alternative', '0'),
(134, 39, 'Directories', 'directories', '0'),
(135, 39, 'Internet Broadcasts', 'internet-broadcasts', '0'),
(136, 39, 'Journals', 'journals', '0'),
(137, 39, 'Magazines and E-zines', 'magazines-and-e-zines', '0'),
(138, 39, 'Newspapers', 'newspapers', '0'),
(139, 39, 'Television', 'television', '0'),
(140, 39, 'Weblogs', 'weblogs', '0'),
(141, 40, 'Astronomy', 'astronomy', '0'),
(142, 40, 'Autos', 'autos', '0'),
(143, 40, 'Aviation', 'aviation', '0'),
(144, 40, 'Birding', 'birding', '0'),
(145, 40, 'Boating', 'boating', '0'),
(146, 40, 'Bowling', 'bowling', '0'),
(147, 40, 'Camps', 'camps', '0'),
(148, 40, 'Climbing', 'climbing', '0'),
(149, 40, 'Collecting', 'collecting', '0'),
(150, 40, 'Crafts', 'crafts', '0'),
(151, 40, 'Drugs', 'drugs', '0'),
(152, 40, 'Food', 'food', '0'),
(153, 40, 'Gambling', 'gambling', '0'),
(154, 40, 'Games', 'games', '0'),
(155, 40, 'Gardening', 'gardening', '0'),
(156, 40, 'Genealogy', 'genealogy', '0'),
(157, 40, 'Guns', 'guns', '0'),
(158, 40, 'Horoscopes', 'horoscopes', '0'),
(159, 40, 'Humor', 'humor', '0'),
(160, 40, 'Kites', 'kites', '0'),
(161, 40, 'Knives', 'knives', '0'),
(162, 40, 'Martial Arts', 'martial-arts', '0'),
(163, 40, 'Models', 'models', '0'),
(164, 40, 'Motorcycles', 'motorcycles', '0'),
(165, 40, 'Nudism', 'nudism', '0'),
(166, 40, 'Outdoors', 'outdoors', '0'),
(167, 40, 'Parties', 'parties', '0'),
(168, 40, 'Pets', 'pets', '0'),
(169, 40, 'Roads and Highways', 'roads-and-highways', '0'),
(170, 40, 'Tobacco', 'tobacco', '0'),
(171, 40, 'Trains and Railroads', 'trains-and-railroads', '0'),
(172, 40, 'Travel', 'travel', '0'),
(173, 41, 'Almanacs', 'almanacs', '0'),
(174, 41, 'Archives', 'archives', '0'),
(175, 41, 'Bibliography', 'bibliography', '0'),
(176, 41, 'Biography', 'biography', '0'),
(177, 41, 'Books', 'books', '0'),
(178, 41, 'Dictionaries', 'dictionaries', '0'),
(179, 41, 'Directories', 'directories', '0'),
(180, 41, 'Encyclopedias', 'encyclopedias', '0'),
(181, 41, 'Journals', 'journals', '0'),
(182, 41, 'Maps', 'maps', '0'),
(183, 41, 'Questions and Answers', 'questions-and-answers', '0'),
(184, 41, 'Quotations', 'quotations', '0'),
(185, 41, 'Thesauri', 'thesauri', '0'),
(186, 42, 'Africa', 'africa', '0'),
(187, 42, 'Asia', 'asia', '0'),
(188, 42, 'Caribbean', 'caribbean', '0'),
(189, 42, 'Central America', 'central-america', '0'),
(190, 42, 'Europe', 'europe', '0'),
(191, 42, 'Middle East', 'middle-east', '0'),
(192, 42, 'North America', 'north-america', '0'),
(193, 42, 'Oceania', 'oceania', '0'),
(194, 42, 'Polar Regions', 'polar-regions', '0'),
(195, 42, 'South America', 'south-america', '0'),
(196, 43, 'Agriculture', 'agriculture', '0'),
(198, 43, 'Astronomy', 'astronomy', '0'),
(199, 43, 'Biology', 'biology', '0'),
(200, 43, 'Chemistry', 'chemistry', '0'),
(201, 43, 'Computer Science', 'computer-science', '0'),
(202, 43, 'Earth Sciences', 'earth-sciences', '0'),
(203, 43, 'Environment', 'environment', '0'),
(204, 43, 'Math', 'math', '0'),
(205, 43, 'Physics', 'physics', '0'),
(206, 43, 'Science in Society', 'science-in-society', '0'),
(207, 43, 'Social Sciences', 'social-sciences', '0'),
(208, 43, 'Technology', 'technology', '0'),
(209, 44, 'Antiques and Collectibles', 'antiques-and-collectibles', '0'),
(210, 44, 'Autos', 'autos', '0'),
(211, 44, 'Beauty Products', 'beauty-products', '0'),
(212, 45, 'Activism', 'activism', '0'),
(213, 45, 'Advice', 'advice', '0'),
(214, 45, 'Crime', 'crime', '0'),
(215, 45, 'Death', 'death', '0'),
(216, 45, 'Economics', 'economics', '0'),
(217, 46, 'Adventure Racing', 'adventure-racing', '0'),
(218, 46, 'Airsoft', 'airsoft', '0'),
(219, 46, 'Animal Sports', 'animal-sports', '0'),
(220, 46, 'Archery', 'archery', '0'),
(221, 46, 'Badminton', 'badminton', '0'),
(224, 44, 'Clothing', 'clothing', '0'),
(225, 44, 'Computers', 'computers', '0'),
(226, 44, 'Consumer Electronics', 'consumer-electronics', '0'),
(227, 44, 'Crafts', 'crafts', '0'),
(228, 44, 'Death Care', 'death-care', '0'),
(229, 44, 'Education', 'education', '0'),
(230, 44, 'Entertainment', 'entertainment', '0'),
(231, 44, 'Flowers', 'flowers', '0'),
(232, 44, 'Food', 'food', '0'),
(233, 44, 'Furniture', 'furniture', '0'),
(234, 44, 'General Merchandise', 'general-merchandise', '0'),
(235, 44, 'Gifts', 'gifts', '0'),
(236, 44, 'Health', 'health', '0'),
(237, 44, 'Holidays', 'holidays', '0'),
(238, 44, 'Home and Garden', 'home-and-garden', '0'),
(239, 44, 'Jewelry', 'jewelry', '0'),
(240, 44, 'Music', 'music', '0'),
(241, 44, 'Niche', 'niche', '0'),
(242, 44, 'Office Products', 'office-products', '0'),
(243, 44, 'Pets', 'pets', '0'),
(244, 44, 'Photography', 'photography', '0'),
(245, 44, 'Publications', 'publications', '0'),
(246, 44, 'Recreation', 'recreation', '0'),
(247, 44, 'Religious', 'religious', '0'),
(248, 44, 'Sports', 'sports', '0'),
(249, 44, 'Tobacco', 'tobacco', '0'),
(250, 44, 'Tools', 'tools', '0'),
(251, 44, 'Toys and Games', 'toys-and-games', '0'),
(252, 44, 'Travel', 'travel', '0'),
(253, 44, 'Vehicles', 'vehicles', '0'),
(254, 44, 'Visual Arts', 'visual-arts', '0'),
(255, 44, 'Weddings', 'weddings', '0'),
(256, 45, 'Education', 'education', '0'),
(257, 45, 'Ethnicity', 'ethnicity', '0'),
(258, 45, 'Folklore', 'folklore', '0'),
(259, 45, 'Future', 'future', '0'),
(260, 45, 'Gay Lesbian and Bisexual', 'gay-lesbian-and-bisexual', '0'),
(261, 45, 'Genealogy', 'genealogy', '0'),
(262, 45, 'Government', 'government', '0'),
(263, 45, 'History', 'history', '0'),
(264, 45, 'Holidays', 'holidays', '0'),
(265, 45, 'Issues', 'issues', '0'),
(266, 45, 'Language and Linguistics', 'language-and-linguistics', '0'),
(267, 45, 'Law', 'law', '0'),
(268, 45, 'Lifestyle Choices', 'lifestyle-choices', '0'),
(269, 45, 'Men', 'men', '0'),
(270, 45, 'Military', 'military', '0'),
(271, 45, 'Paranormal', 'paranormal', '0'),
(272, 45, 'Organizations', 'organizations', '0'),
(273, 45, 'People', 'people', '0'),
(274, 45, 'Philanthropy', 'philanthropy', '0'),
(275, 45, 'Philosophy', 'philosophy', '0'),
(276, 45, 'Politics', 'politics', '0'),
(277, 45, 'Relationships', 'relationships', '0'),
(278, 45, 'Religion and Spirituality', 'religion-and-spirituality', '0'),
(279, 45, 'Sexuality', 'sexuality', '0'),
(280, 45, 'Social Sciences', 'social-sciences', '0'),
(281, 45, 'Sociology', 'sociology', '0'),
(282, 45, 'Subcultures', 'subcultures', '0'),
(283, 45, 'Support Groups', 'support-groups', '0'),
(284, 45, 'Transgendered', 'transgendered', '0'),
(285, 45, 'Urban Legends', 'urban-legends', '0'),
(286, 45, 'Women', 'women', '0'),
(287, 45, 'Work', 'work', '0'),
(288, 46, 'Baseball', 'baseball', '0'),
(289, 46, 'Basketball', 'basketball', '0'),
(290, 46, 'Billiards', 'billiards', '0'),
(291, 46, 'Bocce', 'bocce', '0'),
(292, 46, 'Boomerang', 'boomerang', '0'),
(293, 46, 'Bowling', 'bowling', '0'),
(294, 46, 'Boxing', 'boxing', '0'),
(295, 46, 'Caving', 'caving', '0'),
(296, 46, 'Cheerleading', 'cheerleading', '0'),
(297, 46, 'Cricket', 'cricket', '0'),
(298, 46, 'Croquet', 'croquet', '0'),
(299, 46, 'Cycling', 'cycling', '0'),
(300, 46, 'Darts', 'darts', '0'),
(301, 46, 'Equestrian', 'equestrian', '0'),
(302, 46, 'Extreme Sports', 'extreme-sports', '0'),
(303, 46, 'Fencing', 'fencing', '0'),
(304, 46, 'Fishing', 'fishing', '0'),
(305, 46, 'Flying Discs', 'flying-discs', '0'),
(306, 46, 'Footbag', 'footbag', '0'),
(307, 46, 'Football', 'football', '0'),
(308, 46, 'Gaelic', 'gaelic', '0'),
(309, 46, 'Goalball', 'goalball', '0'),
(310, 46, 'Golf', 'golf', '0'),
(311, 46, 'Greyhound Racing', 'greyhound-racing', '0'),
(312, 46, 'Gymnastics', 'gymnastics', '0'),
(313, 46, 'Handball', 'handball', '0'),
(314, 46, 'Hockey', 'hockey', '0'),
(315, 46, 'Horse Racing', 'horse-racing', '0'),
(316, 46, 'Hunting', 'hunting', '0'),
(317, 46, 'Lacrosse', 'lacrosse', '0'),
(318, 46, 'Martial Arts', 'martial-arts', '0'),
(319, 46, 'Motorsports', 'motorsports', '0'),
(320, 46, 'Paintball', 'paintball', '0'),
(321, 46, 'Racquetball', 'racquetball', '0'),
(322, 46, 'Running', 'running', '0'),
(323, 46, 'Shooting', 'shooting', '0'),
(324, 1, 'Financing', 'financing', '0'),
(325, 1, 'Human Resources', 'human-resources', '0'),
(326, 1, 'Import Export', 'import-export', '0'),
(327, 1, 'Leadership', 'leadership', '0'),
(328, 1, 'Network Marketing', 'network-marketing', '0'),
(329, 1, 'Non Profit Organizations', 'non-profit-organizations', '0'),
(330, 1, 'Project Management', 'project-management', '0'),
(331, 1, 'Public Company', 'public-company', '0'),
(332, 1, 'Sales', 'sales', '0'),
(333, 37, 'Work Life Balance', 'work-life-balance', '0'),
(334, 19, 'Data Recovery', 'data-recovery', '0'),
(335, 19, 'Databases', 'databases', '0'),
(336, 19, 'Networks', 'networks', '0'),
(337, 19, 'Programming', 'programming', '0'),
(338, 36, 'Fitness', 'fitness', '0'),
(339, 36, 'Nutrition', 'nutrition', '0'),
(340, 40, 'Photography', 'photography', '0'),
(341, 1, 'Internet Marketing', 'internet-marketing', '0'),
(342, 1, 'Legal', 'legal', '0'),
(343, 46, 'Soccer', 'soccer', '0'),
(344, 2, 'Banking', 'banking', '0'),
(345, 2, 'Credit', 'credit', '0'),
(346, 2, 'Currency Trading', 'currency-trading', '0'),
(347, 2, 'Financial Planning', 'financial-planning', '0'),
(348, 2, 'Investing', 'investing', '0'),
(349, 2, 'Leasing', 'leasing', '0'),
(350, 2, 'Mortgage', 'mortgage', '0'),
(351, 2, 'Personal Finance', 'personal-finance', '0'),
(352, 2, 'Real Estate', 'real-estate', '0'),
(353, 2, 'Stock Market Investing', 'stock-market-investing', '0'),
(354, 2, 'Structured Settlements', 'structured-settlements', '0'),
(355, 2, 'Taxes', 'taxes', '0'),
(356, 2, 'Wealth Building', 'wealth-building', '0');";

?>