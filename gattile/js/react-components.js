const { useState, useEffect } = React;

function CatalogoGatti() {
	const [listaGatti, setListaGatti] = useState([]);
	const [chiaveRicerca, setChiaveRicerca] = useState("");
	const [criterioOrdine, setCriterioOrdine] = useState("nome");
	const [gattiSelezionati, setGattiSelezionati] = useState([]);
	const [utenteAutenticato, setUtenteAutenticato] = useState(false);

	useEffect(() => {
		// Caricamento asincrono iniziale dei dati tramite endpoint lecture
		fetch("api/gatti.php")
			.then(res => res.json())
			.then(data => setListaGatti(data))
			.catch(err => console.error("Impossibile caricare l'elenco dei gatti", err));

		// Controllo della presenza dello username loggato nell'header della pagina
		if (document.getElementById("loggedUsername")) {
			setUtenteAutenticato(true);
		}
	}, []);

	// Dispatching dell'evento personalizzato ad ogni variazione dei gatti selezionati
	useEffect(() => {
		const eventoPersonalizzato = new CustomEvent("gattiSelezionatiCambiati", {
			detail: { gatti: gattiSelezionati }
		});
		document.dispatchEvent(eventoPersonalizzato);
	}, [gattiSelezionati]);

	const invertiSelezioneGatto = (gattoCambiato) => {
		if (!utenteAutenticato) return; // Impedisce la selezione se l'utente sta navigando da anonimo
        
		if (gattiSelezionati.find(g => g.id === gattoCambiato.id)) {
			setGattiSelezionati(gattiSelezionati.filter(g => g.id !== gattoCambiato.id));
		} else {
			setGattiSelezionati([...gattiSelezionati, gattoCambiato]);
		}
	};

	// Logica di Filtro Client-Side (Nome o Descrizione)
	const gattiFiltrati = listaGatti.filter(g => 
		g.nome.toLowerCase().includes(chiaveRicerca.toLowerCase()) ||
		g.descrizione.toLowerCase().includes(chiaveRicerca.toLowerCase())
	);

	// Logica di Ordinamento Dinamico
	const gattiOrdinati = [...gattiFiltrati].sort((felinoA, felinoB) => {
		if (criterioOrdine === "eta") return felinoA.eta - felinoB.eta;
		if (criterioOrdine === "colore_mantello") return felinoA.colore_mantello.localeCompare(felinoB.colore_mantello);
		if (criterioOrdine === "data_arrivo") return new Date(felinoA.data_arrivo) - new Date(felinoB.data_arrivo);
		return felinoA.nome.localeCompare(felinoB.nome);
	});

	return (
		<section class="catalogo-sezione-interattiva">
			<h2>Catalogo degli Ospiti del Rifugio</h2>
            
			{/* Barra dei Controlli di Ordinamento e Ricerca */}
			<header className="barra-filtri-catalogo">
				<input 
					type="search" 
					placeholder="Digita per filtrare i gatti..." 
					value={chiaveRicerca}
					onChange={(e) => setChiaveRicerca(e.target.value)}
					className="input-ricerca-libera"
					aria-label="Cerca gatti per nome o caratteristiche"
				/>
                
				<select 
					onChange={(e) => setCriterioOrdine(e.target.value)} 
					value={criterioOrdine} 
					className="selettore-ordinamento"
					aria-label="Seleziona il criterio di ordinamento"
				>
					<option value="nome">Ordina alfabeticamente per Nome</option>
					<option value="eta">Ordina per Età crescente</option>
					<option value="colore_mantello">Ordina per Tonalità del Mantello</option>
					<option value="data_arrivo">Ordina per Data di Arrivo</option>
				</select>
			</header>

			{/* Griglia delle schede basata su lista semantica, nessun div superfluo */}
			<ul className="griglia-schede-gatti">
				{gattiOrdinati.map(gatto => {
					const selezionato = gattiSelezionati.some(g => g.id === gatto.id);
					return (
						<li key={gatto.id}>
							<article 
								className={`card-gatto-interattiva ${selezionato ? 'stato-selezionato' : ''} ${utenteAutenticato ? 'stato-abilitato-click' : ''}`}
								onClick={() => invertiSelezioneGatto(gatto)}
							>
								<figure className="contenitore-immagine-gatto">
									<span className="icona-felino-avatar" role="img" aria-label="Icona sagoma gatto">🐱</span>
									<figcaption>
										<h3>{gatto.nome}</h3>
										<p className="biografia-gatto">{gatto.descrizione}</p>
									</figcaption>
								</figure>
								<footer className="specifiche-tecniche-gatto">
									<span><strong>Razza:</strong> {gatto.razza}</span>
									<span><strong>Età:</strong> {gatto.eta} mesi</span>
									<span><strong>Pelo:</strong> {gatto.lunghezza_pelo} ({gatto.colore_mantello})</span>
									<span><strong>Sesso:</strong> {gatto.sesso}</span>
								</footer>
							</article>
						</li>
					);
				})}
			</ul>
		</section>
	);
}

const puntoAggancioCatalogo = ReactDOM.createRoot(document.getElementById('react-catalogo-root'));
puntoAggancioCatalogo.render(<CatalogoGatti />);
