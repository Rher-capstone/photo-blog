function printBlogs(blogArr) {
    // Create and append container
    const printContainer = document.createElement("div");
    printContainer.classList.add("printable");
    printContainer.id = "print-cont";
    document.body.appendChild(printContainer);

    // Page number tracker
    let pageNum = 1;

    // Add Table of Contents Page
    let tocPage;
    const tableOfContents = document.createElement('div');
    tableOfContents.classList.add("table-of-contents");
    printContainer.appendChild(tableOfContents);
    createTOC();

    // Array to keep track of loaded images
    const imagesLoaded = [];

    // Create a page for every blog entry
    blogArr.forEach(blog => {
        // Check if the blog has images
        if (blog.images && blog.images.length > 0) {
            blog.images.forEach(image => {
                createPage(blog, image);
            });
        } else {
            // No image, use default
            createPage(blog);
        }
    });

    // Open print menu after all pages are created
    // Wait for all images to load before triggering the print dialog
    Promise.all(imagesLoaded).then(() => {
        window.print();
    });

    /**
     * Creates and appends a page with a given blog's info and image.
     * Each image gets its own page, or the default image if no image is available.
     *
     * @param {*} blog Blog that is being printed.
     * @param {*} image The image to display for multi-image blogs.
     */
    function createPage(blog, image = undefined) {
        let imgSrc;
        if (image) {
            imgSrc = `images/${blog.blog_id}/${image}`;
        } else {
            // Default image when no image is provided
            imgSrc = "images/photoABCDLogo.png";
        }

        const printElement = document.createElement('div');
        const printHeader = document.createElement('div');
        const blogTitle = document.createElement("h1");
        const printBody = document.createElement('div');
        const blogImage = document.createElement('img');
        const printFooter = document.createElement('div');
        const numBadge = document.createElement('div');
        const blogEventDate = document.createElement("div");
        const dateStr = new Date(blog.event_date).toLocaleDateString(undefined, {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
        });
        const blogDesc = document.createElement("div");

        // Set the page content
        createElements();

        // Increment page number for each page created
        pageNum++;

        function createElements() {
            printElement.classList.add("printelement");

            printHeader.className = "print-header";
            blogTitle.innerHTML = blog.title;
            printHeader.appendChild(blogTitle);

            printBody.className = "print-body";
            blogImage.src = imgSrc;
            printBody.appendChild(blogImage);

            printFooter.className = "print-footer";
            numBadge.className = "page-num";
            numBadge.innerHTML = `Pg. ${pageNum}`;
            blogEventDate.className = "event-date-badge";
            blogEventDate.innerHTML = dateStr;
            blogDesc.className = "desc";
            blogDesc.innerHTML = blog.description;
            printFooter.appendChild(numBadge);
            printFooter.appendChild(blogEventDate);
            printFooter.appendChild(blogDesc);

            printElement.appendChild(printHeader);
            printElement.appendChild(printBody);
            printElement.appendChild(printFooter);
            printContainer.appendChild(printElement);

            // Add entry to the Table of Contents
            entry();

            // Add to imagesLoaded array, and make sure page isn't printed until the image is loaded
            const imgLoadPromise = new Promise((resolve, reject) => {
                blogImage.onload = () => resolve();
                blogImage.onerror = () => reject(`Image failed to load: ${imgSrc}`);
            });

            imagesLoaded.push(imgLoadPromise);
        }

        // Add entry to Table of Contents (TOC)
        function entry() {
            const tableEntry = document.createElement('div');
            tableEntry.className = "table-entry";

            const entryNum = document.createElement('p');
            entryNum.innerHTML = `${pageNum}`;
            entryNum.className = "entry-num";
            tableEntry.appendChild(entryNum);

            const entryTitle = document.createElement('p');
            entryTitle.innerHTML = `${blog.title}`;
            entryTitle.className = "entry-title";
            tableEntry.appendChild(entryTitle);

            tocPage.appendChild(tableEntry);
        }
    }

    /**
     * Create Table of Contents
     */
    function createTOC() {
        const titleWrap = document.createElement('div');
        titleWrap.classList.add("table-title");

        const title = document.createElement('h1');
        title.innerHTML = "Table of Contents";
        titleWrap.appendChild(title);

        const entryContainer = document.createElement('div');
        entryContainer.classList.add("entry-container");

        tocPage = entryContainer;
        tableOfContents.appendChild(titleWrap);
        tableOfContents.appendChild(entryContainer);
    }
}

/**
 * Event Listener. Deletes print elements from body after
 * triggering the afterprint event.
 */
window.addEventListener("afterprint", (event) => {
    const printCont = document.getElementById("print-cont");
    if (printCont) {
        printCont.remove();
    }
});
