// Initial setup for current page, articles per page, and total no. of articles
let currentPage = 1;
const articlesPerPage = 20;
let totalArticles = 0;

// Initial call to load articles
loadArticles();

// Function to load articles
function loadArticles() {
    fetch('php/getNewsdataArticles.php')
        .then(response => response.json())
        .then(data => {
            totalArticles = data.results.length;
            const startIndex = (currentPage - 1) * articlesPerPage;
            const endIndex = startIndex + articlesPerPage;

            const articlesDiv = document.getElementById('newsContainer');
            articlesDiv.innerHTML = ''; // Clear previous articles

            // Loop through the articles and create HTML for each
            data.results.slice(startIndex, endIndex).forEach(article => {
                let imageUrl = article.image_url ? `<img src="${article.image_url}" alt="${article.title}" style="max-width:100%;">` : '';
                let shortContent = article.content.substr(0, 200);
                let moreLink = article.content.length > 200 ? '...<span class="more-link" onclick="showMoreContent(this)">show more</span>' : '';

                // Construct HTML for each article
                let articleHtml = `
                <div class="article">
                ${imageUrl}
                <h2>${article.title}</h2>
                <p>${article.description}</p>
                <div class="article-content">
                    ${shortContent}${moreLink}
                    <span class="full-content" style="display:none;">${article.content}</span>
                </div>
                <p class="source"><strong>Source:</strong> <a href="${article.link}" target="_blank">${article.source_id}</a></p>
                <p class="country"><strong>Country:</strong> ${article.country}</p>
                <a href="#" class="sentiment-link" onclick="event.preventDefault(); handleSentimentClick('${article.content.replace(/["']/g, "")}', this);">Calculate article Sentiment</a>
                <p class="sentiment"></p>
                <p class="sentiment">Sentiment analysis is a computational technique used to detect and interpret emotional tones and subjective opinions within text, enabling the understanding of sentiments expressed in written language. Score refers to the overall emotional leaning of a text (positive, negative, or neutral), while magnitude measures the emotional strength or intensity of that sentiment, regardless of its positive or negative nature.</p>
            </div>`;

                articlesDiv.innerHTML += articleHtml;
            });

            updatePaginationControls();
        });
}

// Function to update pagination controls
function updatePaginationControls() {
    const paginationDiv = document.getElementById('pagination');
    const totalPages = Math.ceil(totalArticles / articlesPerPage);

    paginationDiv.innerHTML = `<span>Page ${currentPage} of ${totalPages}</span>`;

    // Add previous and next buttons if applicable
    if (currentPage > 1) {
        paginationDiv.innerHTML += `<button onclick="changePage(-1)">Previous</button>`;
    }
    if (currentPage < totalPages) {
        paginationDiv.innerHTML += `<button onclick="changePage(1)">Next</button>`;
    }
}

// Function to change the current page
function changePage(delta) {
    currentPage += delta;
    loadArticles();
}

// Function to show more content of an article
function showMoreContent(element) {
    let parentDiv = element.parentElement;
    let fullContent = parentDiv.querySelector('.full-content');
    let shortContentElements = Array.from(parentDiv.childNodes).filter(child => child.nodeType === 3); // Select text nodes

    if (fullContent) {
        element.style.display = 'none'; // Hide 'show more' link
        fullContent.style.display = 'block'; // Display full content

        // Hide the short content
        shortContentElements.forEach(textNode => {
            textNode.textContent = ''; // Clear text node content
        });
    } else {
        console.error('Cannot find the full content element.');
    }
}

// Event listener for the search form submission
document.getElementById("searchForm").addEventListener("submit", function (event) {
    event.preventDefault();

    let formData = new FormData(this);

    let submitButton = this.querySelector('button[type="submit"]');
    submitButton.textContent = 'Searching...'; // Change button text to 'Searching...'

    fetch('php/fetchNewsData.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            let messageElement = document.getElementById("message");
            console.log('Status:', data.status);
            if (data.status === "error") {
                messageElement.style.color = "red";
            } else {
                messageElement.style.color = "green";
            }
            submitButton.textContent = 'Find'; // Reset button text to 'Find'
            location.reload(); // Reload the page
        })
        .catch(error => console.error('Error:', error));
});

// Function to handle sentiment analysis click
function handleSentimentClick(content, sentimentLink) {
    sentimentLink.textContent = 'Please wait... calculating sentiment.';
    getSentimentForText(content, sentimentLink);
}

// Async function to get sentiment for a given text
async function getSentimentForText(text, articleDiv) {
    const response = await fetch('php/getGoogleSentiment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ text: text })
    });

    const data = await response.json();

    // Create sentiment analysis display
    const sentimentDiv = document.createElement('div');
    sentimentDiv.innerHTML = `
        <strong>Sentiment Analysis:</strong>
        <p>Score: ${data.documentSentiment.score}</p>
        <p>Magnitude: ${data.documentSentiment.magnitude}</p>
    `;

    articleDiv.textContent = 'Article Sentiment:';
    displaySentimentBar(data.documentSentiment.score, data.documentSentiment.magnitude, articleDiv);
}

// Function to display sentiment analysis bar
function displaySentimentBar(score, magnitude, container) {
    let color = score > 0 ? "green" : (score < 0 ? "red" : "blue"); // Neutral
    const barWidth = Math.abs(score) * 100; // Convert score to percentage

    const sentimentBar = document.createElement('div');
    sentimentBar.classList.add('sentiment-bar');

    const fillBar = document.createElement('div');
    fillBar.classList.add('fill');
    fillBar.style.width = `${barWidth}%`;
    fillBar.style.backgroundColor = color;

    const toolTip = document.createElement('div');
    toolTip.classList.add('tooltip');
    toolTip.textContent = `Score: ${score}, Magnitude: ${magnitude}`;

    sentimentBar.appendChild(fillBar);
    sentimentBar.appendChild(toolTip);
    container.appendChild(sentimentBar);
}

// Event listener to load articles on DOM content loaded
document.addEventListener('DOMContentLoaded', (event) => {
    loadArticles();
});
