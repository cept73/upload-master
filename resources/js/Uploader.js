export class Uploader
{
    urls = {
        submit: '/upload',
        status: '/status'
    }

    constructor({App, file, onProgress, limitPerSend})
    {
        this.App            = App;
        this.file           = file;
        this.onProgress     = onProgress;
        this.limitPerSend   = limitPerSend;
        this.fileId         = file.size + '-' + file.lastModified;
    }

    async upload()
    {
        this.startByte = await this.getUploadedBytes();

        let blob = this.file.slice(this.startByte, this.startByte + this.limitPerSend);
        if (!blob.size) {
            this.App.hideProgress();
            this.stop();
            return;
        }

        this.App.showProgress();
        let xhr = this.xhr = new XMLHttpRequest();
        xhr.open('POST', this.urls.submit, true);
        xhr.setRequestHeader('X-File-Id', this.fileId);
        xhr.setRequestHeader('X-Start-Byte', this.startByte);
        xhr.setRequestHeader('X-File-Name', this.encodeField(this.file.name));
        xhr.upload.onprogress = () => {
            this.onProgress(this.App, this.startByte, this.file.size);
        }
        xhr.upload.onloadend = () => {
            this.upload();
        }
        xhr.send(blob);
    }

    async getUploadedBytes()
    {
        const headers = {
            'X-File-Id'     : this.fileId,
            'X-File-Name'   : this.encodeField(this.file.name)
        }

        return await fetch(this.urls.status, { headers })
            .then(response => response.json())
            .then(response => {
                if (response.status !== 200) {
                    throw new Error("Can't get uploaded bytes: " + response.statusText);
                }

                let text = response.bytes;

                return +text;
            })
            .catch(err => alert(err));
    }

    encodeField(field)
    {
        return encodeURIComponent(field);
    }

    stop()
    {
        if (this.xhr) {
            this.xhr.upload.onprogress = null;
            this.xhr.abort();
        }
    }
}
