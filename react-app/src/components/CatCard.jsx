function CatCard({
    cat,
    isLogged,
    isSelected,
    onToggle
}) {

    const imagePath =
        "assets/images/cat-placeholder.png";

    return (

        <article
            className={
                isSelected
                    ? "cat-card selected"
                    : "cat-card"
            }
        >

            <img
                src={imagePath}
                alt={`Foto segnaposto del gatto ${cat.nome}`}
                className="cat-image"
            />

            <div className="cat-content">

                <h3>
                    {cat.nome}
                </h3>

                <p>
                    {cat.descrizione}
                </p>

                <dl>

                    <dt>Età</dt>
                    <dd>
                        {cat.eta} mesi
                    </dd>

                    <dt>Sesso</dt>
                    <dd>
                        {cat.sesso}
                    </dd>

                    <dt>Razza</dt>
                    <dd>
                        {cat.razza}
                    </dd>

                    <dt>Mantello</dt>
                    <dd>
                        {cat.colore_mantello}
                    </dd>

                    <dt>Pelo</dt>
                    <dd>
                        {cat.lunghezza_pelo}
                    </dd>

                    <dt>Occhi</dt>
                    <dd>
                        {cat.colore_occhi}
                    </dd>

                    <dt>Peso</dt>
                    <dd>
                        {cat.peso} kg
                    </dd>

                    <dt>Arrivato il</dt>
                    <dd>
                        {cat.data_arrivo}
                    </dd>

                </dl>

                {isLogged && (

                    <div
                        className="selection-area"
                    >

                        <label>

                            <input
                                type="checkbox"
                                checked={isSelected}
                                onChange={() =>
                                    onToggle(cat.id)
                                }
                            />

                            Seleziona per visita

                        </label>

                    </div>

                )}

            </div>

        </article>

    );
}

export default CatCard;