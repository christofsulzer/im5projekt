function loadPosts() {
    fetch('php/getPosts.php')
        .then(response => response.json())
        .then(data => {
            const postsDiv = document.getElementById('posts');
            postsDiv.innerHTML = ''; // Clear previous posts

            data.forEach(post => {
                postsDiv.innerHTML += `
                <div class="post">
                    <strong>${post.display_name} (@${post.username})</strong>
                    <p>${post.content}</p>
                    <small>Erstellt am: ${post.created_at}</small>
                    <div class="card">
                        <h3><a href="${post.card_url}" target="_blank">${post.card_title}</a></h3>
                        <p>${post.card_description}</p>
                        <img src="${post.card_image}" alt="${post.card_title}">
                    </div>
                </div>`;
            });
        });
}

document.addEventListener('DOMContentLoaded', (event) => {
    loadPosts();
});
