/**
 * Form Validation Module
 * Validazione lato client con Vanilla JavaScript
 * WCAG 2.1 AA compliant - ARIA live regions, detailed error messages
 * 
 * @module FormValidator
 */

'use strict';

const FormValidator = (() => {
    /**
     * Configurazione di validazione con regex e messaggi
     */
    const validationRules = {
        username: {
            pattern: /^[a-zA-Z][a-zA-Z0-9_-]{3,49}$/,
            message: 'Username deve iniziare con lettera e contenere solo lettere, numeri, trattini e underscore (4-50 caratteri)',
            required: true
        },
        password: {
            pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,16}$/,
            message: 'Password deve contenere: 8-16 caratteri, almeno una maiuscola, una minuscola, un numero e un carattere speciale (@$!%*?&)',
            required: true
        },
        password_confirm: {
            validator: (value, formElement) => {
                const passwordField = formElement.querySelector('[name="password"]');
                return passwordField && value === passwordField.value;
            },
            message: 'Le password non corrispondono',
            required: true
        },
        email: {
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
            message: 'Inserire un indirizzo email valido',
            required: false
        },
        nome: {
            pattern: /^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s]{2,50}$/,
            message: 'Nome deve contenere solo lettere (2-50 caratteri)',
            required: true
        },
        cognome: {
            pattern: /^[a-zA-ZàèéìòùÀÈÉÌÒÙ\s]{2,50}$/,
            message: 'Cognome deve contenere solo lettere (2-50 caratteri)',
            required: true
        },
        indirizzo: {
            pattern: /^.{5,100}$/,
            message: 'Indirizzo deve essere lungo 5-100 caratteri',
            required: true
        }
    };

    /**
     * Valida un singolo campo
     * 
     * @param {HTMLElement} field Campo input
     * @param {HTMLElement} formElement Form parent
     * @returns {boolean} True se valido
     */
    function validateField(field, formElement) {
        const fieldName = field.name;
        const value = field.value.trim();
        const rule = validationRules[fieldName];

        if (!rule) {
            // Nessuna regola definita - campo valido per default
            return true;
        }

        // Controlla se obbligatorio
        if (rule.required && value === '') {
            showFieldError(field, 'Questo campo è obbligatorio');
            return false;
        }

        // Se non obbligatorio e vuoto, valido
        if (!rule.required && value === '') {
            clearFieldError(field);
            return true;
        }

        // Validazione custom
        if (rule.validator && typeof rule.validator === 'function') {
            const isValid = rule.validator(value, formElement);
            if (!isValid) {
                showFieldError(field, rule.message);
                return false;
            }
        }
        // Validazione regex
        else if (rule.pattern && !rule.pattern.test(value)) {
            showFieldError(field, rule.message);
            return false;
        }

        clearFieldError(field);
        return true;
    }

    /**
     * Mostra errore in un campo
     * 
     * @param {HTMLElement} field Campo input
     * @param {string} message Messaggio di errore
     */
    function showFieldError(field, message) {
        // Trova o crea elemento per errore
        let errorElement = field.parentElement?.querySelector('.error-message');
        if (!errorElement) {
            errorElement = document.createElement('span');
            errorElement.className = 'error-message';
            errorElement.setAttribute('role', 'alert');
            errorElement.setAttribute('aria-live', 'polite');
            field.parentElement.appendChild(errorElement);
        }

        errorElement.textContent = message;
        errorElement.style.display = 'block';
        field.setAttribute('aria-invalid', 'true');
        field.setAttribute('aria-describedby', field.id + '-error');
        field.classList.add('invalid');
        field.parentElement?.classList.add('invalid');
    }

    /**
     * Clearerror da un campo
     * 
     * @param {HTMLElement} field Campo input
     */
    function clearFieldError(field) {
        const errorElement = field.parentElement?.querySelector('.error-message');
        if (errorElement) {
            errorElement.style.display = 'none';
        }
        field.setAttribute('aria-invalid', 'false');
        field.classList.remove('invalid');
        field.parentElement?.classList.remove('invalid');
    }

    /**
     * Mostra messaggio globale nel form
     * 
     * @param {HTMLElement} formElement Form
     * @param {string} message Messaggio
     * @param {string} type Tipo: 'error', 'success', 'info'
     */
    function showFormMessage(formElement, message, type = 'error') {
        let messageElement = formElement.querySelector('.form-message');
        if (!messageElement) {
            messageElement = document.createElement('div');
            messageElement.className = 'form-message alert';
            messageElement.setAttribute('role', type === 'error' ? 'alert' : 'status');
            messageElement.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite');
            formElement.insertBefore(messageElement, formElement.firstChild);
        }

        messageElement.textContent = message;
        messageElement.className = `form-message alert alert-${type}`;
        messageElement.style.display = 'block';
    }

    /**
     * Nasconde messaggio globale del form
     * 
     * @param {HTMLElement} formElement Form
     */
    function hideFormMessage(formElement) {
        const messageElement = formElement.querySelector('.form-message');
        if (messageElement) {
            messageElement.style.display = 'none';
        }
    }

    /**
     * Valida l'intero form
     * 
     * @param {HTMLElement} formElement Form da validare
     * @returns {boolean} True se valido
     */
    function validateForm(formElement) {
        const inputs = formElement.querySelectorAll('input, textarea, select');
        let isValid = true;

        inputs.forEach(input => {
            // Skip button e hidden
            if (input.type === 'button' || input.type === 'submit' || input.type === 'hidden') {
                return;
            }

            if (!validateField(input, formElement)) {
                isValid = false;
            }
        });

        return isValid;
    }

    /**
     * Attiva validazione real-time su un form
     * 
     * @param {HTMLElement} formElement Form
     */
    function enableRealtimeValidation(formElement) {
        const inputs = formElement.querySelectorAll('input, textarea, select');

        inputs.forEach(input => {
            if (input.type === 'button' || input.type === 'submit' || input.type === 'hidden') {
                return;
            }

            // Validazione al blur
            input.addEventListener('blur', () => {
                validateField(input, formElement);
            });

            // Validazione all'input (debounced per performance)
            let debounceTimer;
            input.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    validateField(input, formElement);
                }, 300);
            });

            // Validazione al change
            input.addEventListener('change', () => {
                validateField(input, formElement);
            });
        });
    }

    /**
     * Aggancia validazione a submit del form
     * 
     * @param {HTMLElement} formElement Form
     * @param {Function} onSubmit Callback per submit valido
     */
    function attachFormSubmit(formElement, onSubmit) {
        formElement.addEventListener('submit', (e) => {
            e.preventDefault();

            // Valida tutto il form
            if (!validateForm(formElement)) {
                showFormMessage(formElement, 'Per favore, correggi gli errori nel form', 'error');
                // Focus al primo campo invalido
                const firstInvalid = formElement.querySelector('[aria-invalid="true"]');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
                return;
            }

            hideFormMessage(formElement);
            
            // Chiama callback se valido
            if (typeof onSubmit === 'function') {
                onSubmit(formElement);
            }
        });
    }

    /**
     * Raccoglie i dati di un form in un object
     * 
     * @param {HTMLElement} formElement Form
     * @returns {Object} Dati del form
     */
    function getFormData(formElement) {
        const formData = new FormData(formElement);
        const data = {};

        formData.forEach((value, key) => {
            if (data.hasOwnProperty(key)) {
                // Se la chiave esiste già, convertila in array
                if (!Array.isArray(data[key])) {
                    data[key] = [data[key]];
                }
                data[key].push(value);
            } else {
                data[key] = value;
            }
        });

        return data;
    }

    /**
     * Popola un form con dati
     * 
     * @param {HTMLElement} formElement Form
     * @param {Object} data Dati
     */
    function populateForm(formElement, data) {
        Object.keys(data).forEach(key => {
            const input = formElement.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = data[key];
                } else {
                    input.value = data[key];
                }
            }
        });
    }

    /**
     * Resetta form (svuota e rimuove errori)
     * 
     * @param {HTMLElement} formElement Form
     */
    function resetForm(formElement) {
        formElement.reset();
        
        // Rimuovi tutti gli errori
        const inputs = formElement.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            clearFieldError(input);
        });

        hideFormMessage(formElement);
    }

    /**
     * Disabilita/abilita submit button
     * 
     * @param {HTMLElement} formElement Form
     * @param {boolean} disabled True per disabilitare
     */
    function setFormSubmitDisabled(formElement, disabled) {
        const submitBtn = formElement.querySelector('button[type="submit"], input[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = disabled;
            if (disabled) {
                submitBtn.setAttribute('aria-busy', 'true');
            } else {
                submitBtn.removeAttribute('aria-busy');
            }
        }
    }

    // API pubblica
    return {
        validateField,
        validateForm,
        showFieldError,
        clearFieldError,
        showFormMessage,
        hideFormMessage,
        enableRealtimeValidation,
        attachFormSubmit,
        getFormData,
        populateForm,
        resetForm,
        setFormSubmitDisabled,
        addRule(fieldName, rule) {
            validationRules[fieldName] = rule;
        },
        getRule(fieldName) {
            return validationRules[fieldName];
        }
    };
})();

// Esporta per uso in altri script
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FormValidator;
}
