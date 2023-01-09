import './bootstrap';
import { Uploader } from "./Uploader";

class App
{
    constructor()
    {
        this.uploadForm     = document.querySelector('#upload-form');
        this.submitButton   = this.uploadForm.querySelector('[type=submit]');
        this.progressBar    = document.querySelector('#progress-bar');
        this.inputFile      = this.uploadForm.querySelector('[type=file]');

        this.inputFile.onchange = () => {
            this.onChangeFile();
        }
    }

    onChangeFile()
    {
        this.submitButton.disabled = !this.inputFile.value ? 'disabled' : '';
    }

    onProgress(App, current, maximum)
    {
        let progress = maximum > 0 ? Math.floor(current / maximum * 100) : 0;

        App.progressBar.style.setProperty('width', progress + '%');
        App.progressBar.setAttribute('aria-valuenow', '' + progress);
    }

    showProgress()
    {
        this.setProgressVisibility(false);
    }

    hideProgress()
    {
        this.setProgressVisibility(true);
        this.onProgress(this, 0, 100);
        this.inputFile.value = null;
        this.onChangeFile();
    }

    setProgressVisibility(isVisible)
    {
        this.progressBar.parentElement.hidden = isVisible;
    }

    async sendFilePartial (inputFile)
    {
        let file = inputFile.files[0];
        if (!file) {
            return;
        }

        this.uploader = new Uploader({
            App         : this,
            file        : file,
            onProgress  : this.onProgress,
            limitPerSend: 1024768
        });

        try {
            return await this.uploader.upload();
        } catch (err) {
            console.error(err);
        }
    };

    run()
    {
        this.uploadForm.addEventListener('submit', (event) => {
            event.preventDefault();

            this.submitButton.disabled = true;
            this.sendFilePartial(this.uploadForm.querySelector('[type=file]'))
                .catch(e => { alert(e) });
        })
    }
}

(new App).run();
