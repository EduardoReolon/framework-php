<script>
    function multiSelectEvents(index, default_value = '', separator = ', ') {
        const chBoxes = 
            document.querySelectorAll(`.dropdown-menu.index-multiselect-${index} input[type="checkbox"]`); 
        const dpBtn =  
            document.getElementById(`multiSelectDropdown-${index}`); 
        let mySelectedListItems = [];
    
        function handleCB() { 
            mySelectedListItems = []; 
            let mySelectedListItemsText = ''; 
    
            chBoxes.forEach((checkbox) => { 
                if (checkbox.checked) { 
                    mySelectedListItems.push(checkbox.value); 
                    mySelectedListItemsText += checkbox.getAttribute('value_to_show') + separator; 
                }
            }); 
    
            dpBtn.innerHTML = 
                mySelectedListItems.length > 0 
                    ? mySelectedListItemsText.slice(0, -2) : default_value; 
        } 
    
        chBoxes.forEach((checkbox) => { 
            checkbox.addEventListener('change', handleCB); 
        }); 
        handleCB();
    }

    /** @param {string|null} value */
    function castValue(value, valueIfNull = null, valueIfEmpty = '') {
        if (value === null) return valueIfNull;
        if (value === '') return valueIfEmpty;
        if (value.match(/^true$/i)) return true;
        if (value.match(/^false$/i)) return false;
        if (value.match(/^[0-9]+$/)) return parseInt(value);
        if (value.match(/^\d+\.\d+$/)) return parseFloat(value);
        return value;
    }

    /**
     * @param {HTMLFormElement|{action: string, method: 'post'|'get'|'patch'}} formElement
     * @param {FormData} formData ignorado se formElement for instancia de HTMLFormElement
     * @param {bool} refreshPage ignorado se formElement for instancia de HTMLFormElement
     */
    function sendForm(formElement, formData = null, refreshPage = false) {
        event.preventDefault();

        if (formElement instanceof HTMLFormElement) {
            formData = new FormData(formElement);
            refreshPage = castValue(formElement.getAttribute('refresh-page'), false, true);
            formElement.classList.add('form-disabled');
        }

        let return_value = null;

        // Exemplo de envio assíncrono dos dados usando fetch API
        fetch(formElement.action, {
            method: formElement.method,
            body: formElement.method.match(/^get$/i) ? undefined : formData,
        })
        .then(async response => {
            if (response.ok && refreshPage) return location.reload();

            if (formElement instanceof HTMLFormElement) formElement.classList.remove('form-disabled');

            let serverFailureAlert = !response.ok;

            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/xml')) {
                // Extrai o nome do arquivo do cabeçalho Content-Disposition
                const contentDisposition = response.headers.get('content-disposition');
                const filenameMatch = contentDisposition.match(/filename="(.+)"/);
                const filename = filenameMatch ? filenameMatch[1] : 'arquivo.xml';
                
                // Transforma a resposta em XML
                response = await response.text();

                // Cria um blob a partir do conteúdo XML
                const blob = new Blob([response], { type: 'application/xml' });

                // Cria uma URL temporária para o blob
                const url = window.URL.createObjectURL(blob);

                // Cria um link para download do arquivo
                const link = document.createElement('a');
                link.href = url;
                link.download = filename;

                // Clica no link para iniciar o download
                link.click();

                // Limpa a URL temporária
                window.URL.revokeObjectURL(url);
            } else if (contentType && contentType.includes('application/json')) {
                response = await response.json();

                if (response.redirect) {
                    window.location.href = response.location;
                }

                if (Array.isArray(response.alerts)) {
                    for (const alert of response.alerts) {
                        sendAlert(alert.type, alert.msg, alert.time);
                        serverFailureAlert = false;
                    }
                }

                return_value = response;
            }

            if (serverFailureAlert) {
                sendAlert('warning', 'Falha no servidor!');
            }
        })
        .catch(error => {
            // Lidar com erros de requisição, como falha na conexão, etc.
            console.log(error);
            sendAlert('warning', 'Falha na página, ao salvar as alterações!');
        });

        return return_value;
    }

    /**
     * Definir onSubmit de todos os forms API
     */
    function setOnSubmitForms() {
        const forms = document.querySelectorAll('form');
        for (const form of forms) {
            // if (form.method === 'get') continue;
            if (!form.action.match(/\/api\//i)) continue;
            form.addEventListener('submit', (event) => sendForm(event.target));
        }
    }

    /**
     * @param {'primary'|'secondary'|'success'|'danger'|'warning'|'info'|'light'|'dark'} type
     */
    function sendAlert(type = 'success', msg = '', time = 30000) {
        const container = document.getElementById('conteiner_global_alerts');

        let svg_id = null;
        if (type === 'success') svg_id = 'check-circle-fill';
        else if (type === 'danger') svg_id = 'exclamation-triangle-fill';
        else if (type === 'warning') svg_id = 'exclamation-triangle-fill';
        else if (type === 'primary') svg_id = 'info-fill';
        else if (type === 'secondary') svg_id = 'info-fill';
        else if (type === 'info') svg_id = 'info-fill';

        const divAlert = document.createElement('div');
        divAlert.classList.add('alert', 'alert-dismissible', 'd-flex', 'align-items-center', `alert-${type}`);
        divAlert.role = 'alert';

        if (svg_id !== null) {
            // não está funcionando, talvez precise clonar o svg e inserir aqui
            const svg = document.createElement('svg');
            svg.classList.add('bi', 'flex-shrink-0', 'me-2');
            svg.setAttribute('width', '25');
            svg.setAttribute('height', '25');
            svg.setAttribute('role', 'img');
            svg.setAttribute('aria-label', 'Success:');
            svg.innerHTML = `<use xlink:href="#${svg_id}"/>`;
            divAlert.append(svg);
        }
        const text = document.createElement('div');
        text.innerHTML = msg;
        divAlert.append(text);

        const button = document.createElement('button');
        button.type = 'button';
        button.classList.add('btn-close');
        button.setAttribute('data-bs-dismiss', 'alert');
        button.ariaLabel = 'Close';
        divAlert.append(button);

        setTimeout(() => {
            button.click();
        }, time);

        container.append(divAlert);

        setTimeout(() => {
            container.scrollIntoView({
                behavior: 'smooth', // opcional: rolagem suave
                block: 'start' // opcional: alinha o topo do elemento com o topo da viewport
            });
        }, 100);
    }
</script>