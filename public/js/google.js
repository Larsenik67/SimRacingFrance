var url = 'https://newsapi.org/v2/top-headlines?' +

'sources=google-news-fr&' +
            'q=sport automobile&' +
          'apiKey=5b608b9ceb5444d88cfb632eace9b051';

var req = new Request(url);
fetch(req)
    .then(function(response) {
        console.log(response.json());
    })