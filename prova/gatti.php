<?php
/**
 * Gatti Page
 * Visualizzazione gatti con componente React
 * Form prenotazione visite in Vanilla JS
 * Comunicazione via CustomEvent
 */

'use strict';

session_start();

require_once __DIR__ . '/includes/auth.php';

if (isUserAuthenticated()) {
    checkSessionTimeout();
}

$page_title = 'Gatti - Gattile';
$is_authenticated = isUserAuthenticated();
?>
<?php require_once __DIR__ . '/includes/header.php'; ?>

<div class="container">
    <section aria-label="Galleria gatti e prenotazione visite">
        <h2>I nostri Gatti</h2>
        <p class="section-subtitle">
            Scopri i nostri meravigliosi felini disponibili per l'adozione
        </p>

        <div class="gatti-page-wrapper">
            <!-- React Component per visualizzazione gatti -->
            <div id="react-cats-app" class="cats-app-container"></div>

            <!-- Form Vanilla JS per prenotazione visite (solo se autenticato) -->
            <?php if ($is_authenticated): ?>
                <aside id="booking-sidebar" class="booking-sidebar" role="complementary" aria-label="Prenotazione visita">
                    <h3>Prenota una Visita</h3>
                    <form id="booking-form" class="booking-form" novalidate>
                        <div class="selected-cats-summary">
                            <h4>Gatti selezionati:</h4>
                            <div id="selected-cats-list" class="selected-cats-list">
                                <p class="no-selection">Nessun gatto selezionato</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="visit-date">Data della Visita *</label>
                            <input 
                                type="date" 
                                id="visit-date" 
                                name="visit_date" 
                                required
                                aria-required="true"
                                min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                            >
                            <span class="error-message" role="alert" aria-live="polite"></span>
                        </div>

                        <div class="form-group">
                            <label for="visit-time">Ora della Visita *</label>
                            <select id="visit-time" name="visit_time" required aria-required="true">
                                <option value="">-- Seleziona orario --</option>
                                <option value="09:00">09:00</option>
                                <option value="10:00">10:00</option>
                                <option value="11:00">11:00</option>
                                <option value="14:00">14:00</option>
                                <option value="15:00">15:00</option>
                                <option value="16:00">16:00</option>
                                <option value="17:00">17:00</option>
                            </select>
                            <span class="error-message" role="alert" aria-live="polite"></span>
                        </div>

                        <button type="submit" class="btn btn-primary" id="booking-submit">
                            Prenota Visita
                        </button>
                    </form>
                </aside>
            <?php else: ?>
                <aside class="booking-sidebar login-required" role="complementary">
                    <h3>Per prenotare una visita</h3>
                    <p>
                        Devi essere loggato per selezionare i gatti e prenotare una visita.
                    </p>
                    <div class="booking-buttons">
                        <a href="login.php?redirect=gatti.php" class="btn btn-primary">
                            Accedi
                        </a>
                        <a href="registrazione.php" class="btn btn-secondary">
                            Registrati
                        </a>
                    </div>
                </aside>
            <?php endif; ?>
        </div>
    </section>
</div>

<style>
    .section-subtitle {
        font-size: var(--font-size-large);
        color: var(--color-text-light);
        text-align: center;
        margin-bottom: var(--spacing-xl);
    }

    .gatti-page-wrapper {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: var(--spacing-lg);
        align-items: start;
    }

    .cats-app-container {
        background-color: var(--color-bg-alt);
        border-radius: var(--border-radius-lg);
        padding: var(--spacing-lg);
    }

    .booking-sidebar {
        background-color: white;
        border: 2px solid var(--color-border);
        border-radius: var(--border-radius-lg);
        padding: var(--spacing-lg);
        height: fit-content;
        position: sticky;
        top: 100px;
    }

    .booking-sidebar h3 {
        margin-top: 0;
        color: var(--color-primary);
        border-bottom: 2px solid var(--color-secondary);
        padding-bottom: var(--spacing-md);
    }

    .selected-cats-summary {
        background-color: var(--color-bg-alt);
        padding: var(--spacing-md);
        border-radius: var(--border-radius-md);
        margin-bottom: var(--spacing-lg);
    }

    .selected-cats-summary h4 {
        margin: 0 0 var(--spacing-md) 0;
        font-size: 14px;
    }

    .selected-cats-list {
        min-height: 40px;
    }

    .selected-cat-item {
        background-color: white;
        padding: var(--spacing-sm) var(--spacing-md);
        border-radius: var(--border-radius-md);
        margin-bottom: var(--spacing-sm);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-left: 3px solid var(--color-secondary);
    }

    .no-selection {
        color: var(--color-text-light);
        font-size: var(--font-size-small);
        margin: 0;
        font-style: italic;
    }

    .booking-form .form-group {
        margin-bottom: var(--spacing-lg);
    }

    .booking-form label {
        display: block;
        margin-bottom: var(--spacing-sm);
        font-weight: 600;
        font-size: var(--font-size-small);
    }

    .booking-form input,
    .booking-form select {
        width: 100%;
        padding: var(--spacing-sm);
        border: 2px solid var(--color-border);
        border-radius: var(--border-radius-md);
        font-size: var(--font-size-small);
    }

    .booking-form input:focus,
    .booking-form select:focus {
        outline: none;
        border-color: var(--color-secondary);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .booking-form .btn {
        width: 100%;
        margin-top: var(--spacing-md);
    }

    .booking-sidebar.login-required {
        text-align: center;
        background-color: var(--color-bg-alt);
        border-color: var(--color-warning);
    }

    .booking-sidebar.login-required p {
        margin-bottom: var(--spacing-lg);
    }

    .booking-buttons {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-md);
    }

    .booking-buttons a {
        text-decoration: none;
    }

    @media (max-width: 1024px) {
        .gatti-page-wrapper {
            grid-template-columns: 1fr;
        }

        .booking-sidebar {
            position: static;
        }
    }
</style>

<!-- React Library (CDN) -->
<script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

<!-- React Cats Component -->
<script type="text/babel">
'use strict';

const CatsGallery = () => {
    const [cats, setCats] = React.useState([]);
    const [filteredCats, setFilteredCats] = React.useState([]);
    const [selectedCats, setSelectedCats] = React.useState([]);
    const [loading, setLoading] = React.useState(true);
    const [error, setError] = React.useState(null);
    const [searchTerm, setSearchTerm] = React.useState('');
    const [sortBy, setSortBy] = React.useState('data_arrivo');
    const [sortOrder, setSortOrder] = React.useState('DESC');

    const isAuthenticated = <?php echo $is_authenticated ? 'true' : 'false'; ?>;

    // Carica gatti da API
    React.useEffect(() => {
        fetchCats();
    }, []);

    // Filtra e ordina quando cambiano i parametri
    React.useEffect(() => {
        applyFiltersAndSort();
    }, [cats, searchTerm, sortBy, sortOrder]);

    // Notifica il form vanilla JS quando la selezione cambia
    React.useEffect(() => {
        if (isAuthenticated && typeof CustomEvent === 'function') {
            const event = new CustomEvent('catsSelected', {
                detail: {
                    selectedCatIds: selectedCats,
                    selectedCatsData: cats.filter(c => selectedCats.includes(c.id))
                }
            });
            document.dispatchEvent(event);
        }
    }, [selectedCats]);

    const fetchCats = async () => {
        try {
            setLoading(true);
            const response = await fetch('/PAI/prova/api/gatti-api.php?action=all');
            if (!response.ok) {
                throw new Error('Errore nel caricamento dei dati');
            }
            const data = await response.json();
            if (data.success) {
                setCats(data.data);
                setFilteredCats(data.data);
            } else {
                setError(data.error || 'Errore nel caricamento');
            }
        } catch (err) {
            console.error('Fetch error:', err);
            setError('Errore di connessione al server');
        } finally {
            setLoading(false);
        }
    };

    const applyFiltersAndSort = () => {
        let filtered = cats.filter(cat => {
            const search = searchTerm.toLowerCase();
            return cat.nome.toLowerCase().includes(search) || 
                   cat.descrizione.toLowerCase().includes(search);
        });

        // Ordina
        filtered.sort((a, b) => {
            let aVal = a[sortBy];
            let bVal = b[sortBy];

            // Conversione per data_arrivo (es: "15/01/2026")
            if (sortBy === 'data_arrivo') {
                aVal = new Date(a.data_arrivo.split('/').reverse().join('-'));
                bVal = new Date(b.data_arrivo.split('/').reverse().join('-'));
            }

            if (aVal < bVal) return sortOrder === 'ASC' ? -1 : 1;
            if (aVal > bVal) return sortOrder === 'ASC' ? 1 : -1;
            return 0;
        });

        setFilteredCats(filtered);
    };

    const toggleCatSelection = (catId) => {
        if (!isAuthenticated) return;
        
        setSelectedCats(prev =>
            prev.includes(catId)
                ? prev.filter(id => id !== catId)
                : [...prev, catId]
        );
    };

    const clearSelection = () => {
        setSelectedCats([]);
    };

    if (loading) {
        return (
            <div className="loading-container" aria-busy="true" aria-label="Caricamento gatti in corso">
                <p>Caricamento dei gatti in corso...</p>
                <progress max="100" aria-label="Progresso del caricamento"></progress>
            </div>
        );
    }

    if (error) {
        return (
            <div className="error-container" role="alert">
                <h3>⚠️ Errore</h3>
                <p>{error}</p>
                <button onClick={fetchCats} className="btn btn-secondary">
                    Riprova
                </button>
            </div>
        );
    }

    return (
        <div className="cats-gallery">
            {/* Toolbar di ricerca e filtro */}
            <div className="gallery-toolbar">
                <div className="search-box">
                    <label htmlFor="search-cats">Ricerca:</label>
                    <input
                        type="text"
                        id="search-cats"
                        placeholder="Cerca per nome o descrizione..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                        aria-label="Ricerca gatti"
                    />
                </div>

                <div className="sort-controls">
                    <label htmlFor="sort-select">Ordina per:</label>
                    <select
                        id="sort-select"
                        value={sortBy}
                        onChange={(e) => setSortBy(e.target.value)}
                        aria-label="Criterio di ordinamento"
                    >
                        <option value="data_arrivo">Data arrivo</option>
                        <option value="eta">Età</option>
                        <option value="nome">Nome</option>
                        <option value="colore_mantello">Colore mantello</option>
                    </select>

                    <select
                        value={sortOrder}
                        onChange={(e) => setSortOrder(e.target.value)}
                        aria-label="Ordine di ordinamento"
                    >
                        <option value="DESC">Decrescente</option>
                        <option value="ASC">Crescente</option>
                    </select>
                </div>

                {isAuthenticated && selectedCats.length > 0 && (
                    <div className="selection-info">
                        <span>{selectedCats.length} gatto/i selezionato/i</span>
                        <button onClick={clearSelection} className="btn-clear" type="button">
                            Cancella selezione
                        </button>
                    </div>
                )}
            </div>

            {/* Lista gatti */}
            {filteredCats.length === 0 ? (
                <div className="no-results" role="status">
                    <p>Nessun gatto trovato con i criteri di ricerca.</p>
                </div>
            ) : (
                <div className="cats-grid">
                    {filteredCats.map(cat => (
                        <article
                            key={cat.id}
                            className={`cat-card ${isAuthenticated && selectedCats.includes(cat.id) ? 'selected' : ''}`}
                            role="article"
                            aria-label={`Gatto: ${cat.nome}, Età: ${cat.eta} mesi`}
                        >
                            {isAuthenticated && (
                                <input
                                    type="checkbox"
                                    id={`cat-${cat.id}`}
                                    checked={selectedCats.includes(cat.id)}
                                    onChange={() => toggleCatSelection(cat.id)}
                                    className="cat-checkbox"
                                    aria-label={`Seleziona ${cat.nome} per la visita`}
                                />
                            )}

                            <figure>
                                <picture>
                                    <source srcSet={cat.immagine} type="image/svg+xml" />
                                    <img
                                        src={cat.immagine}
                                        alt={`Foto di ${cat.nome}`}
                                        loading="lazy"
                                    />
                                </picture>
                                <figcaption>
                                    <h4>{cat.nome}</h4>
                                    <p className="cat-description">{cat.descrizione}</p>
                                    <dl className="cat-details">
                                        <div>
                                            <dt>Età:</dt>
                                            <dd>{cat.eta} mese/i</dd>
                                        </div>
                                        <div>
                                            <dt>Colore:</dt>
                                            <dd>{cat.colore_mantello}</dd>
                                        </div>
                                        <div>
                                            <dt>Peso:</dt>
                                            <dd>{cat.peso} kg</dd>
                                        </div>
                                        <div>
                                            <dt>Arrivato:</dt>
                                            <dd>{cat.data_arrivo}</dd>
                                        </div>
                                    </dl>
                                </figcaption>
                            </figure>
                        </article>
                    ))}
                </div>
            )}

            <div className="results-summary" role="status" aria-live="polite">
                {filteredCats.length} gatto/i trovato/i
            </div>
        </div>
    );
};

const root = ReactDOM.createRoot(document.getElementById('react-cats-app'));
root.render(<CatsGallery />);
</script>

<!-- Vanilla JS per form prenotazione visite -->
<script>
'use strict';

document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.getElementById('booking-form');
    if (!bookingForm) return; // Non autenticato

    const selectedCatsList = document.getElementById('selected-cats-list');
    const bookingSubmit = document.getElementById('booking-submit');
    let selectedCatIds = [];

    // Ascolta evento customizzato dal componente React
    document.addEventListener('catsSelected', function(event) {
        selectedCatIds = event.detail.selectedCatIds;
        updateSelectedCatsList(event.detail.selectedCatsData);
    });

    function updateSelectedCatsList(catsData) {
        selectedCatsList.innerHTML = '';

        if (catsData.length === 0) {
            selectedCatsList.innerHTML = '<p class="no-selection">Nessun gatto selezionato</p>';
            bookingSubmit.disabled = true;
            return;
        }

        catsData.forEach(cat => {
            const item = document.createElement('div');
            item.className = 'selected-cat-item';
            item.innerHTML = `
                <span>${cat.nome}</span>
                <span style="color: var(--color-text-light); font-size: 12px;">
                    ${cat.eta} mesi
                </span>
            `;
            selectedCatsList.appendChild(item);
        });

        bookingSubmit.disabled = false;
    }

    // Validazione form
    bookingForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (selectedCatIds.length === 0) {
            alert('Seleziona almeno un gatto');
            return;
        }

        const visitDate = document.getElementById('visit-date').value;
        const visitTime = document.getElementById('visit-time').value;

        if (!visitDate || !visitTime) {
            alert('Compila tutti i campi obbligatori');
            return;
        }

        // Disabilita submit durante il caricamento
        FormValidator.setFormSubmitDisabled(bookingForm, true);

        try {
            const response = await fetch('/PAI/prova/api/booking-api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    visit_date: visitDate,
                    visit_time: visitTime,
                    cat_ids: selectedCatIds
                })
            });

            const data = await response.json();

            if (data.success) {
                FormValidator.showFormMessage(bookingForm, '✓ Visita prenotata con successo!', 'success');
                setTimeout(() => {
                    bookingForm.reset();
                    selectedCatsList.innerHTML = '<p class="no-selection">Nessun gatto selezionato</p>';
                }, 2000);
            } else {
                FormValidator.showFormMessage(bookingForm, data.error || 'Errore nella prenotazione', 'error');
            }
        } catch (err) {
            console.error('Booking error:', err);
            FormValidator.showFormMessage(bookingForm, 'Errore di connessione', 'error');
        } finally {
            FormValidator.setFormSubmitDisabled(bookingForm, false);
        }
    });
});
</script>

<style>
    .loading-container {
        text-align: center;
        padding: var(--spacing-xl);
    }

    .error-container {
        background-color: #f8d7da;
        border: 2px solid #f5c6cb;
        color: #721c24;
        padding: var(--spacing-lg);
        border-radius: var(--border-radius-md);
    }

    .gallery-toolbar {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
        padding: var(--spacing-lg);
        background-color: white;
        border-radius: var(--border-radius-md);
        border: 1px solid var(--color-border);
    }

    .search-box,
    .sort-controls {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-sm);
    }

    .search-box label,
    .sort-controls label {
        font-weight: 600;
        font-size: var(--font-size-small);
    }

    .search-box input,
    .sort-controls select {
        padding: var(--spacing-sm) var(--spacing-md);
        border: 2px solid var(--color-border);
        border-radius: var(--border-radius-md);
        font-size: var(--font-size-small);
    }

    .sort-controls {
        grid-column: span 2;
        flex-direction: row;
        gap: var(--spacing-md);
    }

    .selection-info {
        grid-column: span 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--spacing-md);
        background-color: var(--color-bg-alt);
        border-radius: var(--border-radius-md);
    }

    .btn-clear {
        background-color: var(--color-error);
        color: white;
        padding: var(--spacing-sm) var(--spacing-md);
        border: none;
        border-radius: var(--border-radius-md);
        cursor: pointer;
        font-size: var(--font-size-small);
    }

    .btn-clear:hover {
        background-color: #a93226;
    }

    .cats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: var(--spacing-lg);
    }

    .cat-card {
        position: relative;
        border: 2px solid transparent;
        border-radius: var(--border-radius-lg);
        overflow: hidden;
        cursor: pointer;
        transition: var(--transition);
        background-color: white;
    }

    .cat-card:hover {
        border-color: var(--color-secondary);
        box-shadow: var(--shadow-md);
    }

    .cat-card.selected {
        border-color: var(--color-success);
        background-color: #d4edda;
    }

    .cat-checkbox {
        position: absolute;
        top: var(--spacing-md);
        right: var(--spacing-md);
        z-index: 10;
        width: 24px;
        height: 24px;
        cursor: pointer;
    }

    .cat-card figure {
        margin: 0;
        padding: 0;
    }

    .cat-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }

    .cat-card figcaption {
        padding: var(--spacing-md);
    }

    .cat-card h4 {
        margin: 0 0 var(--spacing-sm) 0;
        color: var(--color-primary);
    }

    .cat-description {
        font-size: var(--font-size-small);
        margin: 0 0 var(--spacing-md) 0;
        line-height: 1.4;
    }

    .cat-details {
        background-color: var(--color-bg-alt);
        padding: var(--spacing-sm);
        border-radius: var(--border-radius-sm);
        margin: 0;
    }

    .cat-details > div {
        display: flex;
        justify-content: space-between;
        padding: 2px 0;
        font-size: 12px;
    }

    .cat-details dt {
        font-weight: 600;
    }

    .no-results,
    .results-summary {
        text-align: center;
        padding: var(--spacing-lg);
        color: var(--color-text-light);
    }

    @media (max-width: 768px) {
        .gallery-toolbar {
            grid-template-columns: 1fr;
        }

        .sort-controls {
            grid-column: span 1 !important;
        }

        .selection-info {
            grid-column: span 1 !important;
        }

        .cats-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }
    }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
