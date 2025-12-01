<div id="rating-modal" class="rating-modal">
    <div class="rating-modal__content">
        <span class="rating-modal__close" onclick="document.getElementById('rating-modal').style.display='none'">&times;</span>
        <h3 class="rating-modal__title">取引相手を評価してください</h3>
        <div class="rating-modal__stars">
            <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="5 stars">★</label>
            <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="4 stars">★</label>
            <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="3 stars">★</label>
            <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="2 stars">★</label>
            <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="1 star">★</label>
        </div>
        <input type="hidden" name="item_id" value="{{ $item->id }}">
        <button type="submit" class="rating-modal__submit-button">評価を送信して取引完了</button>
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
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }

    .rating-modal__content {
        background-color: white;
        padding: 20px;
        border-radius: 5px;
        text-align: center;
        position: relative;
    }

    .rating-modal__close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        cursor: pointer;
    }

    .rating-modal__stars {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        margin: 20px 0;
    }

    .rating-modal__stars input {
        display: none;
    }

    .rating-modal__stars label {
        font-size: 30px;
        color: #ccc;
        cursor: pointer;
    }

    .rating-modal__stars input:checked~label {
        color: #f5b50a;
    }

    .rating-modal__stars label:hover,
    .rating-modal__stars label:hover~label {
        color: #f5b50a;
    }

    .rating-modal__submit-button {
        background-color: #ff5555;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }
</style>