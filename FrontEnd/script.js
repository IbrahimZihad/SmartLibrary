let cart = [];
let wishlist = [];

// Example books data (you can later fetch this from an API or database)
const books = [
    { id: 1, title: "Clean Code", author: "Robert C. Martin" },
    { id: 2, title: "Artificial Intelligence", author: "Stuart Russell" },
    { id: 3, title: "The Pragmatic Programmer", author: "Andy Hunt" },
    { id: 4, title: "Design Patterns", author: "Erich Gamma" },
    { id: 5, title: "Intro to Algorithms", author: "Cormen" },
];

// Function to load books into the book list
function loadBooks() {
    const booksContainer = document.getElementById('books-container');
    books.forEach(book => {
        const bookElement = document.createElement('div');
        bookElement.innerHTML = `
            <h4>${book.title}</h4>
            <p>by ${book.author}</p>
            <button onclick="addToCart(${book.id}, '${book.title}')">Add to Cart</button>
            <button onclick="addToWishlist('${book.title}')">Add to Wishlist</button>
        `;
        booksContainer.appendChild(bookElement);
    });
}

// Add a book to the cart
function addToCart(id, title) {
    if (cart.length >= 3) {
        alert("You can only borrow a maximum of 3 books.");
        return;
    }
    cart.push({ id, title });
    updateCart();
}

// Add a book to the wishlist
function addToWishlist(title) {
    if (!wishlist.includes(title)) {
        wishlist.push(title);
        alert(`${title} added to wishlist.`);
        updateWishlist();
    }
}

// Remove a book from the wishlist
function removeFromWishlist(title) {
    wishlist = wishlist.filter(item => item !== title);
    updateWishlist();
}

// Update the cart display
function updateCart() {
    const cartItems = document.getElementById('cart-items');
    cartItems.innerHTML = '';
    cart.forEach(item => {
        const li = document.createElement('li');
        li.innerHTML = `${item.title} <button onclick="removeFromCart(${item.id})">Remove</button>`;
        cartItems.appendChild(li);
    });
}

// Remove a book from the cart
function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    updateCart();
}

// Update the wishlist display
function updateWishlist() {
    const wishlistItems = document.getElementById('wishlist-items');
    wishlistItems.innerHTML = '';
    wishlist.forEach(item => {
        const li = document.createElement('li');
        li.innerHTML = `${item} <button onclick="removeFromWishlist('${item}')">Remove</button>`;
        wishlistItems.appendChild(li);
    });
}

// Initialize the page by loading books
window.onload = loadBooks;

// Example student data for the student list (In real implementation, this should be fetched from the backend)
const students = [
    { id: 'S001', name: 'John Doe', email: 'johndoe@example.com', phone: '1234567890' },
    { id: 'S002', name: 'Jane Smith', email: 'janesmith@example.com', phone: '0987654321' }
];

// Function to load student list into the table
function loadStudentList() {
    const studentTableBody = document.querySelector('table tbody');
    students.forEach(student => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${student.id}</td>
            <td>${student.name}</td>
            <td>${student.email}</td>
            <td>${student.phone}</td>
            <td><button onclick="deleteStudent('${student.id}')">Delete</button></td>
        `;
        studentTableBody.appendChild(row);
    });
}

// Function to delete a student (For now, it just removes the student from the array)
function deleteStudent(id) {
    const studentIndex = students.findIndex(student => student.id === id);
    if (studentIndex !== -1) {
        students.splice(studentIndex, 1);
        loadStudentList(); // Reload the student list after deletion
    }
}

// Handle the form submission for adding books
const addBookForm = document.getElementById('add-book-form');
addBookForm.addEventListener('submit', function(event) {
    event.preventDefault();

    const bookName = document.getElementById('book-name').value;
    const bookAuthor = document.getElementById('book-author').value;
    const bookCopies = document.getElementById('book-copies').value;

    // In a real application, send this data to the backend to save it to the database.
    console.log(`Book Added: ${bookName} by ${bookAuthor} - ${bookCopies} copies`);

    // Reset the form after submission
    addBookForm.reset();
});

