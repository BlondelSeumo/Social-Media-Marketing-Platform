<?php 

/**
 * 		
 */
class Existing_social_accounts_handler
{
	private $comboposter;
	
	function __construct($comboposter_handler)
	{
		$this->comboposter = $comboposter_handler;
	}


	public function facebook($user_id)
	{

		$where['where'] = array(
		    'user_id' => $user_id
		);
		$existing_accounts = $this->comboposter->basic->get_data('facebook_rx_fb_user_info', $where);

		if(!empty($existing_accounts)) {

		    $i = 0;
		    foreach($existing_accounts as $value) {

		        $existing_account_info[$i]['fb_id'] = $value['fb_id'];
		        $existing_account_info[$i]['userinfo_table_id'] = $value['id'];
		        $existing_account_info[$i]['name'] = $value['name'];
		        $existing_account_info[$i]['email'] = $value['email'];
		        $existing_account_info[$i]['user_access_token'] = $value['access_token'];

		        $where = array();
		        $where['where'] = array(
		            'facebook_rx_fb_user_info_id' => $value['id']
		        );
		        $select = array(
		        	'id', 'facebook_rx_fb_user_info_id', 'page_id', 'page_profile', 'page_name', 'username'
		        );
		        $page_count = $this->comboposter->basic->get_data('facebook_rx_fb_page_info', $where, $select);
		        $existing_account_info[$i]['page_list'] = $page_count;

		        if(!empty($page_count)) {
		            $existing_account_info[$i]['total_pages'] = count($page_count);
		        } else {
		            $existing_account_info[$i]['total_pages'] = 0;
		        }

		        $group_count = $this->comboposter->basic->get_data('facebook_rx_fb_group_info', $where);
		        $existing_account_info[$i]['group_list'] = $group_count;

		        if(!empty($group_count)) {
		            $existing_account_info[$i]['total_groups'] = count($group_count);
		        } else {
		            $existing_account_info[$i]['total_groups'] = 0;
		        }
		        $i++;
		    }
		    return $existing_account_info;
		}  else {
		    return array();
		}
	}

	public function twitter($user_id)
	{
		$where = array(
            'where' => array(
                'user_id' => $user_id
            )
        );

		$twitter_users_info = $this->comboposter->basic->get_data('twitter_users_info', $where);

		if (count($twitter_users_info) > 0) {
			return $twitter_users_info;
		} else {
			return array();
		}
	}


	public function tumblr($user_id)
	{
		$where = array(
		    'where' => array(
		        'user_id' => $user_id
		    )
		);

		$tumblr_users_info = $this->comboposter->basic->get_data('tumblr_users_info', $where);

		if (count($tumblr_users_info) > 0) {
			return $tumblr_users_info;
		} else {
			return array();
		}
	}


	public function youtube($user_id)
	{
		
		$where['where'] = array(
		    'user_id' => $user_id
		);
		$channel_list = $this->comboposter->basic->get_data('youtube_channel_list', $where);

		$user_channel_list = array();
		$i = 0;
		foreach($channel_list as $value) {

		    $user_channel_list[$i]['channel_id'] = $value['channel_id'];
		    $user_channel_list[$i]['profile_image'] = $value['profile_image'];
		    $user_channel_list[$i]['title'] = $value['title'];

		    $i++;
		}

		return $user_channel_list;
	}


	public function linkedin($user_id)
	{

		$where = array(
		    'where' => array(
		        'user_id' => $user_id
		    )
		);

		$select = array(
		    'name', 'linkedin_id', 'id', 'profile_pic'
		);

		$linkedin_account_list = $this->comboposter->basic->get_data('linkedin_users_info', $where , $select);

		if (count($linkedin_account_list)> 0) {
			return $linkedin_account_list;
		} else {
			return array();
		}
	}


	public function medium($user_id)
	{

		$where = array(
		    'where' => array(
		        'user_id' => $user_id
		    )
		);

		$select = array(
		    'name', 'medium_id', 'id', 'profile_pic'
		);

		$medium_account_list = $this->comboposter->basic->get_data('medium_users_info', $where , $select);

		if (count($medium_account_list)> 0) {
			return $medium_account_list;
		} else {
			return array();
		}
	}


	public function pinterest($user_id)
	{
		$where['where'] = array('pinterest_users_info.user_id' => $user_id);
		$join = array('pinterest_board_info' => 'pinterest_users_info.id=pinterest_board_info.pinterest_table_id,left');
		$select = array('pinterest_users_info.id', 'user_name', 'board_name', 'pinterest_board_info.id as table_id', 'name', 'image');

		$pinterest_list = $this->comboposter->basic->get_data('pinterest_users_info', $where, $select, $join);

		$pinterest_info = array();
		if(!empty($pinterest_list)) {

		    $i = 0;
		    foreach($pinterest_list as $value) {

		        $pinterest_info[$value['user_name']]['id'] = $value['id'];
		        $pinterest_info[$value['user_name']]['user_name'] = $value['user_name'];
		        $pinterest_info[$value['user_name']]['name'] = $value['name'];
		        $pinterest_info[$value['user_name']]['image'] = $value['image'];
		        $pinterest_info[$value['user_name']]['pinterest_info'][$i]['table_id'] = $value['table_id'];
		        $pinterest_info[$value['user_name']]['pinterest_info'][$i]['board_name'] = $value['board_name'];
		        $i++;
		    }
		}

		return $pinterest_info;
	}


	public function blogger($user_id)
	{
		$where['where'] = array('blogger_users_info.user_id' => $user_id);
		$join = array('blogger_blog_info' => 'blogger_users_info.id=blogger_blog_info.blogger_users_info_table_id,left');
		$select = array('blogger_users_info.id', 'blogger_users_info.name as blogger_name', 'blogger_blog_info.name as blog_name', 'blogger_id', 'blog_id', 'blogger_blog_info.id as table_id', 'picture');

		$blog_list = $this->comboposter->basic->get_data('blogger_users_info', $where, $select, $join);

		// echo "<pre>";print_r($blog_list);exit;
		$blog_info = array();
		if(!empty($blog_list))
		{
		    $i = 0;
		    foreach($blog_list as $value)
		    {
		        $blog_info[$value['blogger_id']]['id'] = $value['id'];
		        $blog_info[$value['blogger_id']]['name'] = $value['blogger_name'];
		        $blog_info[$value['blogger_id']]['picture'] = $value['picture'];
		        $blog_info[$value['blogger_id']]['blog_info'][$i]['table_id'] = $value['table_id'];
		        $blog_info[$value['blogger_id']]['blog_info'][$i]['blog_id'] = $value['blog_id'];
		        $blog_info[$value['blogger_id']]['blog_info'][$i]['blog_name'] = $value['blog_name'];
		        $i++;
		    }
		}
		return $blog_info;
	}


	public function wordpress($user_id)
	{
		$where = array(
		    'where' => array(
		        'user_id' => $user_id
		    )
		);
		$select = array(
			'id', 'blog_id', 'blog_url', 'name', 'icon', 'categories'
		);

		$wordpress_users_info = $this->comboposter->basic->get_data('wordpress_users_info', $where, $select);

		if (count($wordpress_users_info) > 0) {
			return $wordpress_users_info;
		} else {
			return array();
		}
	}

	public function wordpress_self_hosted($user_id)
	{
		$where = array(
		    'where' => array(
		    	'status' => '1',
		        'user_id' => $user_id
		    )
		);
		$select = array(
			'id', 
			'user_id', 
			'domain_name', 
			'user_key', 
			'authentication_key',
			'blog_category',
		);

		$wordpress_self_hosted = $this->comboposter->basic->get_data('wordpress_config_self_hosted', $where, $select);

		if (count($wordpress_self_hosted) > 0) {
			return $wordpress_self_hosted;
		} else {
			return array();
		}
	}

	public function reddit($user_id)
	{

		$where = array(
		    'where' => array(
		        'user_id' => $user_id
		    )
		);

		$select = array(
		    'username', 'id', 'profile_pic', 'url'
		);

		$reddit_account_list = $this->comboposter->basic->get_data('reddit_users_info',  $where, $select);
		$reddit_account_list['subreddits'] = $this->subRedditList();

		return $reddit_account_list;
	}


	public function subRedditList()
	{
	     
	    $subRedditListArr = array(
	    	"0" => "Please select a subreddit",
	        "1200isplenty" => "1200isplenty",
	        "AdventureTime" => "AdventureTime",
	        "Android" => "Android",
	        "Android" => "Android",
	        "Animated" => "Animated",
	        "Anxiety" => "Anxiety",
	        "ArcherFX" => "ArcherFX",
	        "ArtPorn" => "ArtPorn",
	        "AskReddit" => "AskReddit",
	        "AskScience" => "AskScience",
	        "AskScience" => "AskScience",
	        "AskSocialScience" => "AskSocialScience",
	        "Baseball" => "Baseball",
	        "BetterCallSaul" => "BetterCallSaul",
	        "Bitcoin" => "Bitcoin",
	        "Bitcoin" => "Bitcoin",
	        "BobsBurgers" => "BobsBurgers",
	        "Books" => "Books",
	        "BreakingBad" => "BreakingBad",
	        "Cheap_Meals" => "Cheap_Meals",
	        "Classic4chan" => "Classic4chan",
	        "DeepIntoYouTube" => "DeepIntoYouTube",
	        "Discussion" => "Discussion",
	        "DnD" => "DnD",
	        "Doctor Who" => "Doctor Who",
	        "DoesAnybodyElse" => "DoesAnybodyElse",
	        "DunderMifflin" => "DunderMifflin",
	        "EDC" => "EDC",
	        "Economics" => "Economics",
	        "Entrepreneur" => "Entrepreneur",
	        "FlashTV" => "FlashTV",
	        "FoodPorn" => "FoodPorn",
	        "General" => "General",
	        "General" => "General",
	        "General" => "General",
	        "General" => "General",
	        "General" => "General",
	        "HistoryPorn" => "HistoryPorn",
	        "Hockey" => "Hockey",
	        "HybridAnimals" => "HybridAnimals",
	        "IASIP" => "IASIP",
	        "Jokes" => "Jokes",
	        "LetsNotMeet" => "LetsNotMeet",
	        "LifeProTips" => "LifeProTips",
	        "Lifestyle" => "Lifestyle",
	        "Linux" => "Linux",
	        "LiverpoolFC" => "LiverpoolFC",
	        "Netflix" => "Netflix",
	        "Occupation" => "Occupation",
	        "Offensive_Wallpapers" => "Offensive_Wallpapers",
	        "OutOfTheLoop" => "OutOfTheLoop",
	        "PandR" => "PandR",
	        "Pokemon" => "Pokemon",
	        "Seinfeld" => "Seinfeld",
	        "Sherlock" => "Sherlock",
	        "Soccer" => "Soccer",
	        "Sound" => "Sound",
	        "SpacePorn" => "SpacePorn",
	        "Television" => "Television",
	        "TheSimpsons" => "TheSimpsons",
	        "Tinder" => "Tinder",
	        "TrueDetective" => "TrueDetective",
	        "UniversityofReddit" => "UniversityofReddit",
	        "YouShouldKnow" => "YouShouldKnow",
	        "advice" => "advice",
	        "amiugly" => "amiugly",
	        "anime" => "anime",
	        "apple" => "apple",
	        "aquariums" => "aquariums",
	        "askculinary" => "askculinary",
	        "askengineers" => "askengineers",
	        "askengineers" => "askengineers",
	        "askhistorians" => "askhistorians",
	        "askmen" => "askmen",
	        "askphilosophy" => "askphilosophy",
	        "askwomen" => "askwomen",
	        "bannedfromclubpenguin" => "bannedfromclubpenguin",
	        "batman" => "batman",
	        "battlestations" => "battlestations",
	        "bicycling" => "bicycling",
	        "bigbrother" => "bigbrother",
	        "biology" => "biology",
	        "blackmirror" => "blackmirror",
	        "blackpeoplegifs" => "blackpeoplegifs",
	        "budgetfood" => "budgetfood",
	        "business" => "business",
	        "casualiama" => "casualiama",
	        "celebs" => "celebs",
	        "changemyview" => "changemyview",
	        "changemyview" => "changemyview",
	        "chelseafc" => "chelseafc",
	        "chemicalreactiongifs" => "chemicalreactiongifs",
	        "chemicalreactiongifs" => "chemicalreactiongifs",
	        "chemistry" => "chemistry",
	        "coding" => "coding",
	        "college" => "college",
	        "comics" => "comics",
	        "community" => "community",
	        "confession" => "confession",
	        "cooking" => "cooking",
	        "cosplay" => "cosplay",
	        "cosplay" => "cosplay",
	        "crazyideas" => "crazyideas",
	        "cyberpunk" => "cyberpunk",
	        "dbz" => "dbz",
	        "depression" => "depression",
	        "doctorwho" => "doctorwho",
	        "education" => "education",
	        "educationalgifs" => "educationalgifs",
	        "engineering" => "engineering",
	        "entertainment" => "entertainment",
	        "environment" => "environment",
	        "everymanshouldknow" => "everymanshouldknow",
	        "facebookwins" => "facebookwins",
	        "facepalm" => "facepalm",
	        "facepalm" => "facepalm",
	        "fantasy" => "fantasy",
	        "fantasyfootball" => "fantasyfootball",
	        "firefly" => "firefly",
	        "fitmeals" => "fitmeals",
	        "flexibility" => "flexibility",
	        "food" => "food",
	        "funny" => "funny",
	        "futurama" => "futurama",
	        "gadgets" => "gadgets",
	        "gallifrey" => "gallifrey",
	        "gamedev" => "gamedev",
	        "gameofthrones" => "gameofthrones",
	        "geek" => "geek",
	        "gentlemanboners" => "gentlemanboners",
	        "gifs" => "gifs",
	        "girlsmirin" => "girlsmirin",
	        "google" => "google",
	        "gravityfalls" => "gravityfalls",
	        "gunners" => "gunners",
	        "hardbodies" => "hardbodies",
	        "hardware" => "hardware",
	        "harrypotter" => "harrypotter",
	        "history" => "history",
	        "hockey" => "hockey",
	        "houseofcards" => "houseofcards",
	        "howto" => "howto",
	        "humor" => "humor",
	        "investing" => "investing",
	        "japanesegameshows" => "japanesegameshows",
	        "keto" => "keto",
	        "ketorecipes" => "ketorecipes",
	        "languagelearning" => "languagelearning",
	        "law" => "law",
	        "learnprogramming" => "learnprogramming",
	        "lectures" => "lectures",
	        "lego" => "lego",
	        "lifehacks" => "lifehacks",
	        "linguistics" => "linguistics",
	        "literature" => "literature",
	        "loseit" => "loseit",
	        "magicTCG" => "magicTCG",
	        "marvelstudios" => "marvelstudios",
	        "math" => "math",
	        "mrrobot" => "mrrobot",
	        "mylittlepony" => "mylittlepony",
	        "naruto" => "naruto",
	        "nasa" => "nasa",
	        "nbastreams" => "nbastreams",
	        "nosleep" => "nosleep",
	        "nutrition" => "nutrition",
	        "olympics" => "olympics",
	        "onepunchman" => "onepunchman",
	        "paleo" => "paleo",
	        "patriots" => "patriots",
	        "pettyrevenge" => "pettyrevenge",
	        "photoshop" => "photoshop",
	        "physics" => "physics",
	        "pics" => "pics",
	        "pizza" => "pizza",
	        "podcasts" => "podcasts",
	        "poetry" => "poetry",
	        "pokemon" => "pokemon",
	        "preppers" => "preppers",
	        "prettygirls" => "prettygirls",
	        "psychology" => "psychology",
	        "python" => "python",
	        "quotes" => "quotes",
	        "rainmeter" => "rainmeter",
	        "rateme" => "rateme",
	        "reactiongifs" => "reactiongifs",
	        "recipes" => "recipes",
	        "reddevils" => "reddevils",
	        "relationship_advice" => "relationship_advice",
	        "rickandmorty" => "rickandmorty",
	        "running" => "running",
	        "samplesize" => "samplesize",
	        "scifi" => "scifi",
	        "screenwriting" => "screenwriting",
	        "seinfeld" => "seinfeld",
	        "self" => "self",
	        "sex" => "sex",
	        "shield" => "shield",
	        "simpleliving" => "simpleliving",
	        "soccer" => "soccer",
	        "southpark" => "southpark",
	        "stockmarket" => "stockmarket",
	        "stocks" => "stocks",
	        "talesfromtechsupport" => "talesfromtechsupport",
	        "tattoos" => "tattoos",
	        "teachers" => "teachers",
	        "thewalkingdead" => "thewalkingdead",
	        "thinspo" => "thinspo",
	        "tinyhouses" => "tinyhouses",
	        "todayilearned" => "todayilearned",
	        "topgear" => "topgear",
	        "tumblr" => "tumblr",
	        "twinpeaks" => "twinpeaks",
	        "twitch" => "twitch",
	        "vandwellers" => "vandwellers",
	        "vegan" => "vegan",
	        "videos" => "videos",
	        "wallpaper" => "wallpaper",
	        "weathergifs" => "weathergifs",
	        "westworld" => "westworld",
	        "whitepeoplegifs" => "whitepeoplegifs",
	        "wikileaks" => "wikileaks",
	        "wikipedia" => "wikipedia",
	        "woodworking" => "woodworking",
	        "writing" => "writing",
	        "wwe" => "wwe",
	        "youtube" => "youtube",
	        "youtubehaiku" => "youtubehaiku",
	        "zombies" => "zombies"
	    );

	    $subRedditListArr = array_unique($subRedditListArr);

	    return $subRedditListArr;
	}

}