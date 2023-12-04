function loadPosts() {
    fetch('php/getMastodonPosts.php')
        .then(response => response.json())
        .then(data => {
            const postsDiv = document.getElementById('posts');
            postsDiv.innerHTML = ''; // Clear previous posts

            data.forEach(post => {
                let imageHtml = post.card_image ? `<img src="${post.card_image}" alt="${post.card_title}">` : '';

                postsDiv.innerHTML += `
                <div class="post">
                    <strong>${post.display_name} (@${post.username})</strong>
                    <p>${post.content}</p>
                    <small>Erstellt am: ${post.created_at}</small>
                    <div class="card">
                        <h3><a href="${post.card_url}" target="_blank">${post.card_title}</a></h3>
                        <p>${post.card_description}</p>
                        ${imageHtml}
                    </div>
                </div>`;
            });
        });
}


document.getElementById("keywordForm").addEventListener("submit", function(event){
    event.preventDefault();

    let formData = new FormData(this);

    fetch('php/fetchMastodonPosts.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        let messageElement = document.getElementById("message");
        if (data.status === "error") {
            messageElement.style.color = "red";
        } else {
            messageElement.style.color = "green";
        }
        messageElement.textContent = data.message;
    })
    .catch(error => console.error('Error:', error));
});

document.addEventListener('DOMContentLoaded', (event) => {
    loadPosts();
});

