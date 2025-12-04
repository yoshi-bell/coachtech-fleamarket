<div id="rating-modal" class="rating-modal">
    <div class="rating-modal__content">
        <h3 class="rating-modal__title">取引が完了しました。</h3>
        <p class="rating-modal__subtitle">今回の取引相手はどうでしたか？</p>
        <div class="rating-modal__stars">
            <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="5 stars">★</label>
            <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 stars">★</label>
            <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 stars">★</label>
            <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 stars">★</label>
            <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star">★</label>
        </div>
        <input type="hidden" name="item_id" value="{{ $item->id }}">
        <div class="rating-modal__actions">
            <button type="submit" class="rating-modal__submit-button">送信する</button>
        </div>
    </div>
</div>

<style>
    .rating-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        justify-content: center;
        align-items: center;
    }

    .rating-modal__content {
        background-color: #FDFCE6;
        border: 1px solid #000;
        border-radius: 15px;
        text-align: left;
        position: relative;
        width: 600px;
        height: 367px;
        max-width: 90%;
    }

    .rating-modal__title {
        padding: 18px 22px;
        font-size: 35px;
        font-weight: normal;
        margin: 0;
        color: #000000;
        border-bottom: 1px solid #000;
    }

    .rating-modal__subtitle {
        margin: 0;
        padding: 18px 22px;
        font-size: 20px;
        color: #868686;
    }

    .rating-modal__stars {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        padding: 5px 0 22px;
        border-bottom: 1px solid #000;
    }

    .rating-modal__stars input {
        display: none;
    }

    .rating-modal__stars label {
        font-size: 100px;
        line-height: 100px;
        color: #D9D9D9;
        cursor: pointer;
        padding: 0 5px;
    }

    .rating-modal__stars input:checked~label {
        color: #FFF048;
    }

    .rating-modal__stars label:hover,
    .rating-modal__stars label:hover~label {
        color: #FFF048;
    }

    .rating-modal__actions {
        display: flex;
        justify-content: flex-end;
        padding: 14px;
    }

    .rating-modal__submit-button {
        background-color: #FF8282;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: 127px;
        height: 49px;
        line-height: 49px;
        font-size: 24px;
    }

    .rating-modal__submit-button:hover {
        opacity: 0.8;
    }
</style>