/*
setInterval(() => {
    fetch('/plugins/currency/update')
        .then(response => response.json())
        .then(data => {
            document.querySelector('.currency-widget').innerHTML = data.html;
        });
}, 3600000); // Обновлять каждый час*/
