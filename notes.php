<!-- Scent Notes -->
<section class="section scent-notes" id="ingredients">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">The Art of Scent</h2>
            <p class="section-subtitle">Discover the intricate layers that create our unique fragrances.</p>
        </div>
        
        <div class="notes-container">
            <div class="notes-visual">
                <div class="pyramid-container">
                    <div class="pyramid-layer base-layer">
                        <div class="note note-base" data-note="base">
                            <div class="note-icon">
                                <i class="fas fa-mountain"></i>
                            </div>
                            <div class="note-content">
                                <div class="note-name">Base Notes</div>
                                <div class="note-desc">Sandalwood, Musk</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pyramid-layer heart-layer">
                        <div class="note note-heart" data-note="heart">
                            <div class="note-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="note-content">
                                <div class="note-name">Heart Notes</div>
                                <div class="note-desc">Jasmine, Rose</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pyramid-layer top-layer">
                        <div class="note note-top" data-note="top">
                            <div class="note-icon">
                                <i class="fas fa-cloud"></i>
                            </div>
                            <div class="note-content">
                                <div class="note-name">Top Notes</div>
                                <div class="note-desc">Bergamot, Lemon</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="notes-info">
                <div class="note-detail active" id="top-detail">
                    <h3>Top Notes</h3>
                    <p>The initial impression of a fragrance, top notes are the lightest and most volatile scents that you smell immediately after application. They typically evaporate quickly, making way for the heart notes.</p>
                    <div class="note-ingredients">
                        <span class="ingredient-tag">Bergamot</span>
                        <span class="ingredient-tag">Lemon</span>
                        <span class="ingredient-tag">Citrus</span>
                        <span class="ingredient-tag">Light Florals</span>
                    </div>
                </div>
                
                <div class="note-detail" id="heart-detail">
                    <h3>Heart Notes</h3>
                    <p>Also known as middle notes, the heart notes emerge just as the top notes dissipate. These scents form the core of the fragrance and determine its main character.</p>
                    <div class="note-ingredients">
                        <span class="ingredient-tag">Jasmine</span>
                        <span class="ingredient-tag">Rose</span>
                        <span class="ingredient-tag">Lavender</span>
                        <span class="ingredient-tag">Spices</span>
                    </div>
                </div>
                
                <div class="note-detail" id="base-detail">
                    <h3>Base Notes</h3>
                    <p>The final fragrance notes that appear once the top notes have completely evaporated. Base notes are the foundation of the perfume, providing depth and richness that can last for hours.</p>
                    <div class="note-ingredients">
                        <span class="ingredient-tag">Sandalwood</span>
                        <span class="ingredient-tag">Musk</span>
                        <span class="ingredient-tag">Vanilla</span>
                        <span class="ingredient-tag">Amber</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Scent Notes Section - Modern Redesign */
.scent-notes {
    background: linear-gradient(135deg, var(--light-gray) 0%, var(--white) 100%);
    position: relative;
    overflow: hidden;
}

.scent-notes::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100" opacity="0.03"><path d="M50,10 A40,40 0 1,1 50,90 A40,40 0 1,1 50,10 Z" fill="%23d4af37"/></svg>');
    background-size: 150px;
    pointer-events: none;
}

.notes-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
    margin-top: 3rem;
}

.notes-visual {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

.pyramid-container {
    position: relative;
    width: 300px;
    height: 300px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: center;
}

.pyramid-layer {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    transition: all 0.5s ease;
}

.base-layer {
    height: 40%;
    z-index: 1;
}

.heart-layer {
    height: 35%;
    z-index: 2;
}

.top-layer {
    height: 25%;
    z-index: 3;
}

.note {
    background: var(--white);
    border-radius: 20px;
    padding: 1.5rem;
    text-align: center;
    box-shadow: var(--shadow);
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    cursor: pointer;
    width: 180px;
    position: relative;
    overflow: hidden;
}

.note::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--gradient-gold);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.note:hover::before {
    transform: scaleX(1);
}

.note.active {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

.note.active::before {
    transform: scaleX(1);
}

.note-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.note-top .note-icon {
    color: #a66d30; /* Gold accent */
}

.note-heart .note-icon {
    color: #c0c0c0; /* Silver accent */
}

.note-base .note-icon {
    color: #0a0a0a; /* Dark accent */
}

.note-name {
    font-family: 'Cinzel', serif;
    font-weight: 600;
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    color: var(--black);
}

.note-desc {
    font-size: 0.9rem;
    color: var(--muted);
}

.notes-info {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.note-detail {
    display: none;
    animation: fadeIn 0.5s ease forwards;
}

.note-detail.active {
    display: block;
}

.note-detail h3 {
    font-family: 'Cinzel', serif;
    font-size: 1.8rem;
    margin-bottom: 1rem;
    color: var(--black);
    position: relative;
    display: inline-block;
}

.note-detail h3::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--gradient-gold);
}

.note-detail p {
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 1.5rem;
    color: var(--muted);
}

.note-ingredients {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 1.5rem;
}

.ingredient-tag {
    background: rgba(166, 109, 48, 0.1);
    color: var(--black);
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.ingredient-tag:hover {
    background: rgba(166, 109, 48, 0.2);
    transform: translateY(-2px);
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mobile Responsiveness */
@media (max-width: 992px) {
    .notes-container {
        grid-template-columns: 1fr;
        gap: 3rem;
    }
    
    .pyramid-container {
        width: 250px;
        height: 250px;
    }
    
    .note {
        width: 160px;
        padding: 1.2rem;
    }
}

@media (max-width: 768px) {
    .pyramid-container {
        width: 220px;
        height: 220px;
    }
    
    .note {
        width: 140px;
        padding: 1rem;
    }
    
    .note-name {
        font-size: 1.1rem;
    }
    
    .note-desc {
        font-size: 0.85rem;
    }
    
    .note-detail h3 {
        font-size: 1.5rem;
    }
    
    .note-detail p {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .pyramid-container {
        width: 200px;
        height: 200px;
    }
    
    .note {
        width: 130px;
        padding: 0.8rem;
    }
    
    .note-icon {
        font-size: 1.5rem;
        height: 50px;
    }
    
    .ingredient-tag {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
}

/* Touch device optimizations */
@media (hover: none) {
    .note:hover {
        transform: none;
    }
    
    .note.active {
        transform: translateY(-5px);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notes = document.querySelectorAll('.note');
    const noteDetails = document.querySelectorAll('.note-detail');
    
    // Function to activate a note
    function activateNote(noteType) {
        // Remove active class from all notes
        notes.forEach(note => {
            note.classList.remove('active');
        });
        
        // Remove active class from all note details
        noteDetails.forEach(detail => {
            detail.classList.remove('active');
        });
        
        // Add active class to clicked note
        const activeNote = document.querySelector(`.note[data-note="${noteType}"]`);
        if (activeNote) {
            activeNote.classList.add('active');
        }
        
        // Show corresponding detail
        const activeDetail = document.getElementById(`${noteType}-detail`);
        if (activeDetail) {
            activeDetail.classList.add('active');
        }
    }
    
    // Add click event listeners to notes
    notes.forEach(note => {
        note.addEventListener('click', function() {
            const noteType = this.getAttribute('data-note');
            activateNote(noteType);
        });
    });
    
    // Add keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
            e.preventDefault();
            const activeNote = document.querySelector('.note.active');
            let nextNoteType;
            
            if (e.key === 'ArrowUp') {
                if (activeNote.classList.contains('note-top')) {
                    nextNoteType = 'base';
                } else if (activeNote.classList.contains('note-heart')) {
                    nextNoteType = 'top';
                } else {
                    nextNoteType = 'heart';
                }
            } else if (e.key === 'ArrowDown') {
                if (activeNote.classList.contains('note-top')) {
                    nextNoteType = 'heart';
                } else if (activeNote.classList.contains('note-heart')) {
                    nextNoteType = 'base';
                } else {
                    nextNoteType = 'top';
                }
            }
            
            activateNote(nextNoteType);
        }
    });
    
    // Initialize with top note active
    activateNote('top');
});
</script>