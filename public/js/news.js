const settings = {
	"async": true,
	"crossDomain": true,
	"url": "https://free-news.p.rapidapi.com/v1/search?q=Sport%20Automobile&lang=fr",
	"method": "GET",
	"headers": {
		"x-rapidapi-host": "free-news.p.rapidapi.com",
		"x-rapidapi-key": "67ce16a9d6mshbe1abae5c3f8058p1141b8jsne9bbca6431c5"
	}
};

$.ajax(settings).done(function (response) {
	

	var path = window.location.pathname

	console.log(path)

	for(var i=0; i < response.articles.length; i++){ 
		
		

		let source = response.articles[i].clean_url
		let img = response.articles[i].media
		let url = response.articles[i].link
		let titre = response.articles[i].title
		

		if ( path.startsWith("/home") || path.startsWith("/admin") || path == "/"){

			if(titre.length > 90) titre = titre.substring(0,90) + "..."

			$("#vignette_news").append(
				"<a class='nav_link mr black' href='"
				+ url + 
				"'><li><div class='vignette'><div class='picture_div marge'><img class='picture' src='"
				+ img +
				"' alt='News'><div id='div_text'><h4><span class='uppercase' id='titre'>"
				 + titre + 
				 "</span></h4></div></div><div class='source black police_course'><p>"
				 + source + 
				 "</p></div></div></li></a>"
			)

		} else if ( path.startsWith("/news") ){

			if(titre.length > 250) titre = titre.substring(0,250) + "..."

			let date = Date.parse(response.articles[i].published_date)
			var today = Date.now()

			var difference = today - date

			var jour = Math.floor(difference / 86400000.00000055)

			$(".news").append(
				"<a class='animation' href='"
				+ url + 
				"'><div class='vignette'><div class='picture_div marge'><img class='picture' src='"
				+ img +
				"' alt='News'><div id='div_text'><h4><span class='uppercase' id='titre'>"
				 + titre + 
				 "</span></h4></div></div><div class='info_news'><div class='source black police_course'><p>"
				 + source + 
				 "</p></div><div class='jours'><p>Il y'a " 
				 + jour + 
				 " jour(s)</p></div></div></div></a>"
			)
		}
	}
});