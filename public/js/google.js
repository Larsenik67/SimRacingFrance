const data = null;

const xhr = new XMLHttpRequest();
xhr.withCredentials = true;

xhr.addEventListener("readystatechange", function () {
	if (this.readyState === this.DONE) {
        let news = this.responseText 
		console.log(news[10]);

        let titre = news.articles
	}
});

xhr.open("GET", "https://free-news.p.rapidapi.com/v1/search?q=Sport%20Automobile&lang=fr");
xhr.setRequestHeader("x-rapidapi-host", "free-news.p.rapidapi.com");
xhr.setRequestHeader("x-rapidapi-key", "67ce16a9d6mshbe1abae5c3f8058p1141b8jsne9bbca6431c5");

xhr.send(data);