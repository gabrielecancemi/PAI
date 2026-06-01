import { useEffect, useMemo, useState } from "react";
import CatCard from "./CatCard";

function CatGallery() {

    const [cats, setCats] =
        useState([]);

    const [searchTerm, setSearchTerm] =
        useState("");

    const [sortField, setSortField] =
        useState("data_arrivo");

    const [selectedCats, setSelectedCats] =
        useState([]);

    const isLogged =
        window.isLogged ?? false;

    useEffect(() => {

        fetch("api/gatti-api.php")
            .then(response => response.json())
            .then(data => {

                setCats(data);

            })
            .catch(error => {

                console.error(error);

            });

    }, []);

    useEffect(() => {

        document.dispatchEvent(

            new CustomEvent(
                "catsSelected",
                {
                    detail: selectedCats
                }
            )

        );

    }, [selectedCats]);

    const filteredCats =
        useMemo(() => {

            const search =
                searchTerm.toLowerCase();

            let result =
                cats.filter(cat => {

                    return (
                        cat.nome
                            .toLowerCase()
                            .includes(search)
                        ||
                        cat.descrizione
                            .toLowerCase()
                            .includes(search)
                    );

                });

            result.sort((a, b) => {

                if (sortField === "eta") {

                    return Number(a.eta)
                        - Number(b.eta);
                }

                if (
                    sortField ===
                    "colore_mantello"
                ) {

                    return a.colore_mantello
                        .localeCompare(
                            b.colore_mantello
                        );
                }

                return new Date(
                    b.data_arrivo
                ) -
                new Date(
                    a.data_arrivo
                );

            });

            return result;

        }, [
            cats,
            searchTerm,
            sortField
        ]);

    function toggleSelection(catId) {

        if (!isLogged) {

            return;
        }

        setSelectedCats(
            previous => {

                if (
                    previous.includes(catId)
                ) {

                    return previous.filter(
                        id => id !== catId
                    );
                }

                return [
                    ...previous,
                    catId
                ];
            }
        );
    }

    return (

        <section
            className="cats-gallery"
        >

            <div
                className="gallery-controls"
            >

                <div>

                    <label
                        htmlFor="search"
                    >

                        Cerca

                    </label>

                    <input
                        id="search"
                        type="search"
                        value={searchTerm}
                        onChange={
                            event =>
                                setSearchTerm(
                                    event.target.value
                                )
                        }
                        placeholder="Nome o descrizione"
                    />

                </div>

                <div>

                    <label
                        htmlFor="sort"
                    >

                        Ordina per

                    </label>

                    <select
                        id="sort"
                        value={sortField}
                        onChange={
                            event =>
                                setSortField(
                                    event.target.value
                                )
                        }
                    >

                        <option
                            value="data_arrivo"
                        >
                            Data arrivo
                        </option>

                        <option
                            value="eta"
                        >
                            Età
                        </option>

                        <option
                            value="colore_mantello"
                        >
                            Colore mantello
                        </option>

                    </select>

                </div>

            </div>

            <div
                className="cats-grid"
            >

                {filteredCats.map(cat => (

                    <CatCard
                        key={cat.id}
                        cat={cat}
                        isLogged={isLogged}
                        isSelected={
                            selectedCats.includes(
                                cat.id
                            )
                        }
                        onToggle={
                            toggleSelection
                        }
                    />

                ))}

            </div>

        </section>

    );
}

export default CatGallery;