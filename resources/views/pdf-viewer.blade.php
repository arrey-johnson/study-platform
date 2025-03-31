<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PDF Viewer - Study Platform</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.4.120/web/pdf_viewer.min.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        .header {
            background-color: #7e22ce; /* Purple color to match platform */
            color: white;
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            z-index: 10;
            position: relative;
        }
        .header h1 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .header-buttons {
            display: flex;
            gap: 0.75rem;
        }
        .btn {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        .btn svg {
            width: 1rem;
            height: 1rem;
        }
        .container {
            height: calc(100vh - 3.5rem);
            display: flex;
            flex-direction: column;
            position: relative;
        }
        #pdf-container {
            flex: 1;
            position: relative;
            background-color: #e5e7eb;
            overflow: auto;
        }
        #pdf-viewer {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin: 1rem 0;
        }
        .watermark {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 5;
            pointer-events: none;
            opacity: 0.1;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 5rem;
            font-weight: bold;
            color: #7e22ce;
            transform: rotate(-45deg);
        }
        .notice {
            position: fixed;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            z-index: 10;
        }
        .notice svg {
            width: 1rem;
            height: 1rem;
        }
        .controls {
            display: flex;
            justify-content: center;
            padding: 0.5rem;
            background-color: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }
        .controls button {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            margin: 0 0.25rem;
            font-size: 0.875rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .controls button:hover {
            background-color: #e5e7eb;
        }
        .controls .page-info {
            display: flex;
            align-items: center;
            margin: 0 1rem;
            font-size: 0.875rem;
            color: #4b5563;
        }
        .controls input {
            width: 3rem;
            text-align: center;
            margin: 0 0.25rem;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            padding: 0.25rem;
        }
        @media (max-width: 640px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
                padding: 0.75rem 1rem;
            }
            .header-left {
                width: 100%;
                justify-content: flex-start;
            }
            .header-buttons {
                width: 100%;
                justify-content: flex-end;
            }
            .container {
                height: calc(100vh - 5rem);
            }
            .controls {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <a href="{{ url()->previous() }}" class="btn back-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                </svg>
                Back to Chapter
            </a>
            <h1>Chapter Notes</h1>
        </div>
        <div class="header-buttons">
            <button id="close-btn" class="btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Close
            </button>
        </div>
    </div>
    
    <div class="container">
        <div class="controls">
            <button id="prev">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                </svg>
                Previous
            </button>
            <div class="page-info">
                Page <input type="text" id="page-num" min="1" value="1"> of <span id="page-count">0</span>
            </div>
            <button id="next">
                Next
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
            <button id="zoom-in">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                </svg>
                Zoom In
            </button>
            <button id="zoom-out">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8z"/>
                </svg>
                Zoom Out
            </button>
        </div>
        
        <div id="pdf-container">
            <canvas id="pdf-viewer"></canvas>
        </div>
        
        <div class="watermark">
            <span>{{ auth()->user()->name }}</span>
        </div>
        
        <div class="notice">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            This content is protected and cannot be downloaded
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@3.4.120/build/pdf.min.js"></script>
    <script>
        // Configure PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@3.4.120/build/pdf.worker.min.js';
        
        // Variables
        let pdfDoc = null;
        let pageNum = 1;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.0;
        const canvas = document.getElementById('pdf-viewer');
        const ctx = canvas.getContext('2d');
        
        // Load PDF
        const loadPDF = async (url) => {
            try {
                const loadingTask = pdfjsLib.getDocument(url);
                pdfDoc = await loadingTask.promise;
                document.getElementById('page-count').textContent = pdfDoc.numPages;
                
                // Initial render
                renderPage(pageNum);
            } catch (error) {
                console.error('Error loading PDF:', error);
                alert('Failed to load PDF. Please try again later.');
            }
        };
        
        // Render page
        const renderPage = (num) => {
            pageRendering = true;
            
            // Get page
            pdfDoc.getPage(num).then((page) => {
                // Adjust scale based on viewport
                const viewport = page.getViewport({ scale });
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                // Render PDF page
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                
                const renderTask = page.render(renderContext);
                
                // Wait for rendering to finish
                renderTask.promise.then(() => {
                    pageRendering = false;
                    
                    if (pageNumPending !== null) {
                        // New page rendering is pending
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });
            
            // Update page number input
            document.getElementById('page-num').value = num;
        };
        
        // Queue rendering of a page
        const queueRenderPage = (num) => {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        };
        
        // Previous page
        const previousPage = () => {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        };
        
        // Next page
        const nextPage = () => {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        };
        
        // Zoom in
        const zoomIn = () => {
            scale += 0.25;
            queueRenderPage(pageNum);
        };
        
        // Zoom out
        const zoomOut = () => {
            if (scale <= 0.5) return;
            scale -= 0.25;
            queueRenderPage(pageNum);
        };
        
        // Event listeners
        document.getElementById('prev').addEventListener('click', previousPage);
        document.getElementById('next').addEventListener('click', nextPage);
        document.getElementById('zoom-in').addEventListener('click', zoomIn);
        document.getElementById('zoom-out').addEventListener('click', zoomOut);
        
        // Page input
        document.getElementById('page-num').addEventListener('change', function() {
            const newPageNum = parseInt(this.value);
            if (newPageNum >= 1 && newPageNum <= pdfDoc.numPages) {
                pageNum = newPageNum;
                queueRenderPage(pageNum);
            } else {
                this.value = pageNum;
            }
        });
        
        // Close button
        document.getElementById('close-btn').addEventListener('click', function() {
            window.close();
        });
        
        // Security measures
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent right-click
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });
            
            // Prevent keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Prevent Ctrl+S, Ctrl+P, Ctrl+Shift+S, Ctrl+Shift+E, F12
                if ((e.ctrlKey || e.metaKey) && 
                    (e.key === 's' || e.key === 'p' || e.key === 'S' || e.key === 'E' || e.key === 'c' || 
                     (e.shiftKey && (e.key === 'S' || e.key === 'E' || e.key === 'I')))) {
                    e.preventDefault();
                    return false;
                }
                
                // Prevent F12
                if (e.key === 'F12') {
                    e.preventDefault();
                    return false;
                }
                
                // Allow arrow keys for navigation
                if (e.key === 'ArrowLeft') {
                    previousPage();
                    e.preventDefault();
                } else if (e.key === 'ArrowRight') {
                    nextPage();
                    e.preventDefault();
                }
            });
            
            // Disable selection
            document.onselectstart = function() { return false; };
        });
        
        // Load the PDF
        loadPDF('{{ $pdfUrl }}');
    </script>
</body>
</html>
