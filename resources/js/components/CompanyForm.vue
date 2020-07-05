<template>
    <form @submit.prevent="$emit('submit')" @keydown="form.onKeydown($event)">
        <div class="form-row">
            <div class="form-group col-md-7">
                <label for="name">Nom de l'entreprise</label>
                <input type="text"
                       id="name"
                       v-model="form.name"
                       :class="['form-control', form.errors.has('name') ? 'is-invalid' : '' ]">

                <div v-if="form.errors.has('name')"
                     class="invalid-feedback"
                     v-text="form.errors.get('name')">
                </div>
            </div>

            <div class="form-group col-md-5">
                <label for="type_code">Type</label>
                <select id="type_code"
                        v-model="form.type_code"
                        :class="['form-control', form.errors.has('type_code') ? 'is-invalid' : '' ]">
                    <option value="" disabled></option>
                    <option v-for="(type, typeCode) of App.companyTypes"
                            :value="typeCode"
                            v-text="type"
                    ></option>
                </select>

                <div v-if="form.errors.has('type_code')"
                     class="invalid-feedback"
                     v-text="form.errors.get('type_code')">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="website">Site web</label>
            <input type="text"
                   id="website"
                   aria-describedby="websiteHelpBlock"
                   v-model="form.website"
                   :class="['form-control', form.errors.has('website') ? 'is-invalid' : '' ]">

            <small id="websiteHelpBlock" class="form-text text-muted">
                <span class="text-orange">Pense bien à inclure "https://" ou "http://" au début de l'URL.</span><br>
                Le site web est également utilisé pour obtenir le logo de la compagnie.
            </small>

            <div v-if="form.errors.has('website')"
                 class="invalid-feedback"
                 v-text="form.errors.get('website')">
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description"
                      aria-describedby="descriptionHelpBlock"
                      rows="3"
                      v-model="form.description"
                      :class="['form-control', form.errors.has('description') ? 'is-invalid' : '' ]"
            ></textarea>

            <small id="descriptionHelpBlock" class="form-text text-muted">
                Présente la compagnie, ses objectifs, ses particularités...
            </small>

            <div v-if="form.errors.has('description')"
                 class="invalid-feedback"
                 v-text="form.errors.get('description')">
            </div>
        </div>

        <div class="form-group">
            <label for="operations">Opérations</label>
            <textarea id="operations"
                      aria-describedby="operationsHelpBlock"
                      rows="3"
                      v-model="form.operations"
                      :class="['form-control', form.errors.has('operations') ? 'is-invalid' : '' ]"
            ></textarea>

            <small id="operationsHelpBlock" class="form-text text-muted">
                S'il s'agit d'une entreprise avec des activités aériennes, parle nous de sa flotte, du rythme moyen
                des plannings, le nombre d'heures de vol par an...
            </small>

            <div v-if="form.errors.has('operations')"
                 class="invalid-feedback"
                 v-text="form.errors.get('operations')">
            </div>
        </div>

        <div class="form-group">
            <label for="conditions">Conditions</label>
            <textarea id="conditions"
                      aria-describedby="conditionsHelpBlock"
                      rows="3"
                      v-model="form.conditions"
                      :class="['form-control', form.errors.has('conditions') ? 'is-invalid' : '' ]"
            ></textarea>

            <small id="conditionsHelpBlock" class="form-text text-muted">
                Quels types de contrats sont proposés par la compagnie ? Quelle rémunération ? Combien de jours de
                congés payés ? La compagnie propose-t-elle des avantages en nature comme des GP ? Peux-tu nous
                donner une idée du salaire moyen après impôts ?<br>
                Comment se passent la cotisation à une caisse de retraite, l'assurance maladie ?<br>
                La QT est-elle à la charge du salarié ? Y a-t-il prise en charge des frais de licence, de visite
                médicale, de transport, de logement, de nourriture (per diem), etc ?<br>
                Qu'en est-il de la progression de carrière, du temps moyen avant de passer CDB ?
            </small>

            <div v-if="form.errors.has('conditions')"
                 class="invalid-feedback"
                 v-text="form.errors.get('conditions')">
            </div>
        </div>

        <div class="form-group">
            <label for="remarks">Remarques</label>
            <textarea id="remarks"
                      aria-describedby="remarksHelpBlock"
                      rows="3"
                      v-model="form.remarks"
                      :class="['form-control', form.errors.has('remarks') ? 'is-invalid' : '' ]"
            ></textarea>

            <small id="remarksHelpBlock" class="form-text text-muted">
                Toute autre information qui peut te sembler pertinente.
            </small>

            <div v-if="form.errors.has('remarks')"
                 class="invalid-feedback"
                 v-text="form.errors.get('remarks')">
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-outline-danger mr-auto"
                    v-if="showDelete"
                    @click="$emit('destroy')">
                Supprimer
            </button>

            <button type="button" class="btn btn-link mr-2" data-dismiss="modal">
                Annuler
            </button>

            <button type="submit" class="btn btn-success">
                Enregistrer
            </button>
        </div>
    </form>
</template>

<script>
    export default {
        props: {
            form: {
                type: Object,
                required: true,
            },
            showDelete: Boolean,
        },

        watch: {
            form(value) {
                this.$emit('update:form', value);
            },
        }
    }
</script>
